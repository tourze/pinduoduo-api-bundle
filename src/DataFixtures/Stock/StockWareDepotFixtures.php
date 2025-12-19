<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class StockWareDepotFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly StockWareRepository $stockWareRepository,
        private readonly DepotRepository $depotRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的货品和仓库
        $stockWare = $this->stockWareRepository->findOneBy([]);
        $depot = $this->depotRepository->findOneBy([]);

        if (null === $stockWare || null === $depot) {
            return; // 如果没有货品或仓库数据，跳过
        }

        // 创建测试货品仓库库存数据
        $wareDepot = new StockWareDepot();
        $wareDepot->setStockWare($stockWare);
        $wareDepot->setDepot($depot);
        $wareDepot->setAvailableQuantity(100);
        $wareDepot->setTotalQuantity(100);
        $manager->persist($wareDepot);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StockWareFixtures::class,
            DepotFixtures::class,
        ];
    }
}
