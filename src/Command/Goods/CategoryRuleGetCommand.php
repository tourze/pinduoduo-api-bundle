<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Exception\PddApiException;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.cat.rule.get
 */
#[AsCommand(name: self::NAME, description: '测试-类目商品发布规则查询接口')]
final class CategoryRuleGetCommand extends LockableCommand
{
    public const NAME = 'pdd:get-category-rule';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::REQUIRED);
        $this->addArgument('categoryId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mall = $this->mallRepository->find($input->getArgument('mallId'));
        if (!$mall instanceof Mall) {
            return Command::FAILURE;
        }

        if (null !== $input->getArgument('categoryId')) {
            $categories = $this->categoryRepository->findBy(['id' => $input->getArgument('categoryId')]);
        } else {
            $categories = $this->categoryRepository->createQueryBuilder('a')
                ->where('a.level>2')
                ->getQuery()
                ->toIterable()
            ;
        }

        foreach ($categories as $category) {
            if (!$category instanceof Category) {
                continue;
            }

            // 如果有下级，我们要跳过
            if ($category->getChildren()->count() > 0) {
                continue;
            }

            try {
                $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.cat.rule.get', [
                    'cat_id' => $category->getId(),
                ]);
                // $file = __DIR__ . "/../../data/properties/category_{$category->getId()}.json";
                // file_put_contents($file, json_encode($result, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE));
                // 存入到数据库
                $category->setCatRule($result);
                $this->entityManager->persist($category);
                $this->entityManager->flush();
                $output->writeln("{$category->getId()}成功获取发布规则");
            } catch (PddApiException $exception) {
                $output->writeln("{$category->getId()} -> {$exception->getMessage()}[{$exception->getCode()}]: {$exception->getSubMsg()}");
            } catch (\Throwable $exception) {
                $output->writeln("{$category->getId()} -> {$exception->getMessage()}[{$exception->getCode()}]");
            }
        }

        return Command::SUCCESS;
    }
}
