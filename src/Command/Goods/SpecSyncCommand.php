<?php

namespace PinduoduoApiBundle\Command\Goods;

use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\CategoryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.spec.get
 */
#[AsCommand(name: self::NAME, description: '商品属性类目接口')]
final class SpecSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-spec-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->mallRepository->findAll() as $mall) {
            // 文档说：叶子类目ID，必须入参level=3时的cat_id,否则无法返回正确的参数
            $categories = $this->categoryRepository->findBy(['level' => 3]);
            foreach ($categories as $category) {
                $this->categoryService->syncSpecList($mall, $category);
            }
        }

        return Command::SUCCESS;
    }
}
