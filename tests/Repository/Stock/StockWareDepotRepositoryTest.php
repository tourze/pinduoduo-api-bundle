<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;
use PinduoduoApiBundle\Repository\Stock\StockWareDepotRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareDepotRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockWareDepotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(StockWareDepotRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allStockWareDepots = $repository->findAll();
        foreach ($allStockWareDepots as $stockWareDepot) {
            $repository->remove($stockWareDepot);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        // 创建依赖对象
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE');
        $stockWare->setWareName('Test Ware');
        self::getEntityManager()->persist($stockWare);

        $depot = new Depot();
        $depot->setDepotCode('TEST_DEPOT');
        $depot->setDepotName('Test Depot');
        $depot->setDepotAlias('Test Depot Alias');
        $depot->setContact('Test Contact');
        $depot->setPhone('1234567890');
        $depot->setAddress('Test Address');
        $depot->setProvince(1);
        $depot->setCity(1);
        $depot->setDistrict(1);
        $depot->setZipCode('123456');
        self::getEntityManager()->persist($depot);

        $stockWareDepot = new StockWareDepot();
        $stockWareDepot->setStockWare($stockWare);
        $stockWareDepot->setDepot($depot);
        $stockWareDepot->setAvailableQuantity(100);

        $repository->save($stockWareDepot);
    }

    protected function createNewEntity(): StockWareDepot
    {
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE_' . uniqid());
        $stockWare->setWareName('Test Ware ' . uniqid());

        $depot = new Depot();
        $depot->setDepotCode('TEST_DEPOT_' . uniqid());
        $depot->setDepotName('Test Depot ' . uniqid());
        $depot->setDepotAlias('Test Depot Alias ' . uniqid());
        $depot->setContact('Test Contact');
        $depot->setPhone('1234567890');
        $depot->setAddress('Test Address');
        $depot->setProvince(1);
        $depot->setCity(1);
        $depot->setDistrict(1);
        $depot->setZipCode('123456');

        self::getEntityManager()->persist($stockWare);
        self::getEntityManager()->persist($depot);

        $stockWareDepot = new StockWareDepot();
        $stockWareDepot->setStockWare($stockWare);
        $stockWareDepot->setDepot($depot);
        $stockWareDepot->setAvailableQuantity(100);

        return $stockWareDepot;
    }

    protected function getRepository(): StockWareDepotRepository
    {
        return self::getService(StockWareDepotRepository::class);
    }

    public function testFindByStockWareAndDepot(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        $repository->save($entity);

        $stockWareId = $entity->getStockWare()->getId();
        $depotId = $entity->getDepot()->getId();

        if (null === $stockWareId || null === $depotId) {
            self::markTestSkipped('StockWare or Depot ID is null');
        }

        $result = $repository->findByStockWareAndDepot($stockWareId, $depotId);

        $this->assertInstanceOf(StockWareDepot::class, $result);
        $this->assertSame($entity->getId(), $result->getId());
    }

    public function testFindLowStock(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        $entity->setAvailableQuantity(5);
        $entity->setWarningThreshold(10.0);
        $repository->save($entity);

        $result = $repository->findLowStock(0.0);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testFindOverStock(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        $entity->setTotalQuantity(200);
        $entity->setUpperLimit(100.0);
        $repository->save($entity);

        $result = $repository->findOverStock();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testFindByDepot(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        $repository->save($entity);

        $depotId = $entity->getDepot()->getId();
        if (null === $depotId) {
            self::markTestSkipped('Depot ID is null');
        }

        $result = $repository->findByDepot($depotId);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testFindByLocationCode(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        $entity->setLocationCode('LOC-001');
        $repository->save($entity);

        $result = $repository->findByLocationCode('LOC-001');

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }
}
