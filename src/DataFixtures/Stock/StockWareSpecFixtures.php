<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class StockWareSpecFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly StockWareSkuRepository $stockWareSkuRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的SKU
        $stockWareSku = $this->stockWareSkuRepository->findOneBy([]);
        if (null === $stockWareSku) {
            return; // 如果没有SKU数据，跳过
        }

        // 创建测试规格数据
        $spec1 = new StockWareSpec();
        $spec1->setStockWareSku($stockWareSku);
        $spec1->setSpecId('1001');
        $spec1->setSpecKey('颜色');
        $spec1->setSpecValue('红色');
        $manager->persist($spec1);

        $spec2 = new StockWareSpec();
        $spec2->setStockWareSku($stockWareSku);
        $spec2->setSpecId('1002');
        $spec2->setSpecKey('尺寸');
        $spec2->setSpecValue('L');
        $manager->persist($spec2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StockWareSkuFixtures::class,
        ];
    }
}
