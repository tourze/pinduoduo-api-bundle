<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * PDD的商品信息会定时更新
 */
#[AsCommand(name: CategoryLoopSyncCommand::NAME, description: '递归同步商品目录')]
class CategoryLoopSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:loop-sync-goods-category';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SdkService $sdkService,
        private readonly CategoryRepository $categoryRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('parentId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $account = $this->accountRepository->findOneBy([
            'applicationType' => ApplicationType::搬家上货,
        ]);
        if (!$account) {
            $output->writeln("找不到账号：{$account->getId()}");

            return Command::FAILURE;
        }

        $parent = null;
        if ($input->getArgument('parentId')) {
            $parent = $this->categoryRepository->find($input->getArgument('parentId'));
            if (!$parent) {
                $output->writeln('找不到上级目录');

                return Command::FAILURE;
            }
        }

        $sdk = $this->sdkService->getMerchantSdk($account);
        $response = $sdk->api->request('pdd.goods.cats.get', ['parent_cat_id' => $parent ? $parent->getId() : 0]);
        if (!isset($response['goods_cats_get_response'])) {
            $this->logger->error('同步商品目录时发生错误', [
                'response' => $response,
            ]);

            return Command::FAILURE;
        }
        foreach ($response['goods_cats_get_response']['goods_cats_list'] as $item) {
            $category = $this->categoryRepository->find($item['cat_id']);
            if (!$category) {
                $category = new Category();
                $category->setId($item['cat_id']);
            }
            $category->setName($item['cat_name']);
            $category->setLevel($item['level']);
            $category->setParent($parent);
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            // 我们继续往下查找喔
            $message = new RunCommandMessage();
            $message->setCommand(self::NAME);
            $message->setOptions([
                'parentId' => $category->getId(),
            ]);
            $this->messageBus->dispatch($message);

            $this->entityManager->detach($category);
        }

        return Command::SUCCESS;
    }
}
