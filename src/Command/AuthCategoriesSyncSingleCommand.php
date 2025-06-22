<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AuthCatRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.authorization.cats
 */
#[AsCommand(name: self::NAME, description: '同步授权商品目录')]
class AuthCategoriesSyncSingleCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-auth-categories';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly AuthCatRepository $authCatRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('mallId') !== null) {
            $malls = $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        foreach ($malls as $mall) {
            try {
                $this->syncCategories($mall, '0');
            } catch (\Throwable $exception) {
                $this->logger->error('同步门店授权商品目录失败', [
                    'mall' => $mall,
                    'exception' => $exception,
                ]);
            }
        }

        return Command::SUCCESS;
    }

    private function syncCategories(Mall $mall, string $parentCatId): void
    {
        $response = $this->sdkService->request($mall, 'pdd.goods.authorization.cats', [
            'parent_cat_id' => $parentCatId,
        ]);
        if (!isset($response['goods_auth_cats_get_response'])) {
            return;
        }

        dump($response['goods_auth_cats_get_response']);
        foreach ($response['goods_auth_cats_get_response']['goods_cats_list'] as $item) {
            $authCat = $this->authCatRepository->findOneBy([
                'mall' => $mall,
                'catId' => $item['cat_id'],
            ]);
            if ($authCat === null) {
                $authCat = new AuthCat();
                $authCat->setMall($mall);
                $authCat->setCatId($item['cat_id']);
            }
            $authCat->setParentCatId($parentCatId);
            $authCat->setCatName($item['cat_name']);
            $authCat->setLeaf((bool) $item['leaf']);

            $this->entityManager->persist($authCat);
            $this->entityManager->flush();

            $this->entityManager->detach($authCat);

            // 递归查询
            $this->syncCategories($mall, $authCat->getCatId());
        }
    }
}
