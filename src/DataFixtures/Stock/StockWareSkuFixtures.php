<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class StockWareSkuFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly StockWareRepository $stockWareRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的货品
        $stockWare = $this->stockWareRepository->findOneBy([]);
        if (null === $stockWare) {
            return; // 如果没有货品数据，跳过
        }

        // 创建测试SKU数据
        $sku1 = new StockWareSku();
        $sku1->setStockWare($stockWare);
        $sku1->setGoodsId('1001');
        $sku1->setSkuId('10011');
        $sku1->setSkuName('测试SKU1');
        $manager->persist($sku1);

        $sku2 = new StockWareSku();
        $sku2->setStockWare($stockWare);
        $sku2->setGoodsId('1002');
        $sku2->setSkuId('10022');
        $sku2->setSkuName('测试SKU2');
        $manager->persist($sku2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StockWareFixtures::class,
        ];
    }
}
