<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Request\BasePddRequest;
use PinduoduoApiBundle\Service\PinduoduoClient;
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
#[AsCommand(name: self::NAME, description: '递归同步商品目录')]
#[WithMonologChannel(channel: 'pinduoduo_api')]
final class CategoryLoopSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:loop-sync-goods-category';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly PinduoduoClient $pinduoduoClient,
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
        $account = $this->getAccount($output);
        if (null === $account) {
            return Command::FAILURE;
        }

        $parent = $this->getParentCategory($input, $output);
        $parentId = $input->getArgument('parentId');
        if (null !== $parentId && null === $parent) {
            return Command::FAILURE;
        }

        $categoriesData = $this->fetchCategoriesData($account, $parent);
        if (null === $categoriesData) {
            return Command::FAILURE;
        }

        $this->processCategoriesData($categoriesData, $parent);

        return Command::SUCCESS;
    }

    private function getAccount(OutputInterface $output): ?Account
    {
        $account = $this->accountRepository->findOneBy([
            'applicationType' => ApplicationType::搬家上货,
        ]);

        if (null === $account) {
            $output->writeln('找不到账号');

            return null;
        }

        return $account;
    }

    private function getParentCategory(InputInterface $input, OutputInterface $output): ?Category
    {
        $parentId = $input->getArgument('parentId');
        if (null === $parentId) {
            return null;
        }

        $parent = $this->categoryRepository->find($parentId);
        if (null === $parent) {
            $output->writeln('找不到上级目录');

            return null;
        }

        return $parent;
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchCategoriesData(Account $account, ?Category $parent): ?array
    {
        try {
            // 使用 BasePddRequest 直接调用公开 API
            $request = new BasePddRequest();
            $request->setAccount($account);
            $request->setType('pdd.goods.cats.get');
            $request->setParams([
                'parent_cat_id' => null !== $parent ? $parent->getId() : 0,
            ]);

            $response = $this->pinduoduoClient->request($request);

            if (!is_array($response) || !isset($response['goods_cats_list']) || !is_array($response['goods_cats_list'])) {
                return null;
            }

            return $response['goods_cats_list'];
        } catch (\Exception $e) {
            $this->logger->error('同步商品目录时发生错误', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param array<mixed> $categoriesData
     */
    private function processCategoriesData(array $categoriesData, ?Category $parent): void
    {
        foreach ($categoriesData as $item) {
            if (!is_array($item)) {
                continue;
            }
            $category = $this->createOrUpdateCategory($item, $parent);
            $this->scheduleChildSync($category);
            $this->entityManager->detach($category);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function createOrUpdateCategory(array $item, ?Category $parent): Category
    {
        if (!isset($item['cat_id'], $item['cat_name'], $item['level'])) {
            throw new \InvalidArgumentException('缺少必要的商品分类字段');
        }

        $catId = is_string($item['cat_id']) ? $item['cat_id'] : null;
        $catName = is_string($item['cat_name']) ? $item['cat_name'] : '';
        $level = is_int($item['level']) ? $item['level'] : 0;

        $category = $this->categoryRepository->find($catId);
        if (null === $category) {
            $category = new Category();
            $category->setId($catId);
        }

        $category->setName($catName);
        $category->setLevel($level);
        $category->setParent($parent);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    private function scheduleChildSync(Category $category): void
    {
        $message = new RunCommandMessage();
        $message->setCommand(self::NAME);
        $message->setOptions([
            'parentId' => $category->getId(),
        ]);
        $this->messageBus->dispatch($message);
    }
}
