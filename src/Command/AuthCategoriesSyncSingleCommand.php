<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AuthCatRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
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
#[WithMonologChannel(channel: 'pinduoduo_api')]
class AuthCategoriesSyncSingleCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-auth-categories';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
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
        if (null !== $input->getArgument('mallId')) {
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
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.authorization.cats', [
                'parent_cat_id' => $parentCatId,
            ]);
        } catch (\Exception $e) {
            return;
        }

        if (!isset($response['goods_cats_list'])) {
            return;
        }

        // $this->logger->info('同步响应数据', ['response' => $response]);
        $goodsCatsList = $response['goods_cats_list'];
        if (!is_array($goodsCatsList)) {
            return;
        }

        foreach ($goodsCatsList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $catId = $item['cat_id'] ?? null;
            if (!is_string($catId)) {
                continue;
            }

            $authCat = $this->authCatRepository->findOneBy([
                'mall' => $mall,
                'catId' => $catId,
            ]);
            if (null === $authCat) {
                $authCat = new AuthCat();
                $authCat->setMall($mall);
                $authCat->setCatId($catId);
            }
            $authCat->setParentCatId($parentCatId);

            $catName = $item['cat_name'] ?? '';
            if (is_string($catName)) {
                $authCat->setCatName($catName);
            }

            $leaf = $item['leaf'] ?? false;
            $authCat->setLeaf((bool) $leaf);

            $this->entityManager->persist($authCat);
            $this->entityManager->flush();

            $this->entityManager->detach($authCat);

            // 递归查询
            $savedCatId = $authCat->getCatId();
            if (null !== $savedCatId) {
                $this->syncCategories($mall, $savedCatId);
            }
        }
    }
}
