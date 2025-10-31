<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockWareRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(StockWareRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allStockWares = $repository->findAll();
        foreach ($allStockWares as $stockWare) {
            $this->assertInstanceOf(StockWare::class, $stockWare);
            $repository->remove($stockWare);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE');
        $stockWare->setWareName('Test Stock Ware');
        $stockWare->setType(StockWareTypeEnum::NORMAL);

        $repository->save($stockWare);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindStockWare(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('WARE001');
        $stockWare->setWareName('测试货品');
        $stockWare->setSpecification('规格1');
        $stockWare->setUnit('件');
        $stockWare->setBrand('测试品牌');
        $stockWare->setColor('红色');
        $stockWare->setType(StockWareTypeEnum::NORMAL);

        $repository->save($stockWare);

        $foundStockWare = $repository->find($stockWare->getId());
        $this->assertNotNull($foundStockWare);
        $this->assertSame('WARE001', $foundStockWare->getWareSn());
        $this->assertSame('测试货品', $foundStockWare->getWareName());
        $this->assertSame('规格1', $foundStockWare->getSpecification());
        $this->assertSame('件', $foundStockWare->getUnit());
        $this->assertSame('测试品牌', $foundStockWare->getBrand());
        $this->assertSame('红色', $foundStockWare->getColor());
        $this->assertSame(StockWareTypeEnum::NORMAL, $foundStockWare->getType());
    }

    public function testFindOneByWareSn(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('UNIQUE_WARE');
        $stockWare->setWareName('唯一货品');

        $repository->save($stockWare);

        $foundStockWare = $repository->findOneBy(['wareSn' => 'UNIQUE_WARE']);
        $this->assertNotNull($foundStockWare);
        $this->assertSame('UNIQUE_WARE', $foundStockWare->getWareSn());
        $this->assertSame('唯一货品', $foundStockWare->getWareName());
    }

    public function testFindByType(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $ware1 = new StockWare();
        $ware1->setWareSn('NORMAL_WARE');
        $ware1->setWareName('普通货品');
        $ware1->setType(StockWareTypeEnum::NORMAL);

        $ware2 = new StockWare();
        $ware2->setWareSn('VIRTUAL_WARE');
        $ware2->setWareName('虚拟货品');
        $ware2->setType(StockWareTypeEnum::VIRTUAL);

        $repository->save($ware1);
        $repository->save($ware2);

        $normalWares = $repository->findBy(['type' => StockWareTypeEnum::NORMAL]);
        $this->assertNotEmpty($normalWares);

        $virtualWares = $repository->findBy(['type' => StockWareTypeEnum::VIRTUAL]);
        $this->assertNotEmpty($virtualWares);
    }

    public function testFindByWithNullFields(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('NULL_FIELDS_WARE');
        $stockWare->setWareName('空字段货品');
        $stockWare->setSpecification(null);
        $stockWare->setUnit(null);
        $stockWare->setBrand(null);
        $stockWare->setColor(null);
        $stockWare->setPacking(null);
        $stockWare->setNote(null);
        $stockWare->setWareId(null);

        $repository->save($stockWare);

        $waresWithNullSpec = $repository->findBy(['specification' => null]);
        $this->assertNotEmpty($waresWithNullSpec);

        $waresWithNullUnit = $repository->findBy(['unit' => null]);
        $this->assertNotEmpty($waresWithNullUnit);

        $waresWithNullBrand = $repository->findBy(['brand' => null]);
        $this->assertNotEmpty($waresWithNullBrand);

        $waresWithNullColor = $repository->findBy(['color' => null]);
        $this->assertNotEmpty($waresWithNullColor);

        $waresWithNullWareId = $repository->findBy(['wareId' => null]);
        $this->assertNotEmpty($waresWithNullWareId);
    }

    public function testRemoveStockWare(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('TO_REMOVE_WARE');
        $stockWare->setWareName('待删除货品');

        $repository->save($stockWare);
        $id = $stockWare->getId();

        $repository->remove($stockWare);

        $foundStockWare = $repository->find($id);
        $this->assertNull($foundStockWare);
    }

    public function testFindByWareCode(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('CODEWERE001');
        $stockWare->setWareName('测试货品按编码');
        $stockWare->setWareId('UNIQUE_WARE_ID');

        $repository->save($stockWare);

        // 测试通过wareId查找（因为实体没有wareCode字段，使用wareId代替）
        $foundStockWare = $repository->findOneBy(['wareId' => 'UNIQUE_WARE_ID']);
        $this->assertNotNull($foundStockWare);
        $this->assertSame('CODEWERE001', $foundStockWare->getWareSn());
        $this->assertSame('测试货品按编码', $foundStockWare->getWareName());

        $notFoundStockWare = $repository->findOneBy(['wareId' => 'NONEXISTENT_ID']);
        $this->assertNull($notFoundStockWare);
    }

    public function testFindByWareName(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $ware1 = new StockWare();
        $ware1->setWareSn('WARE_NAME_1');
        $ware1->setWareName('同名测试货品');

        $ware2 = new StockWare();
        $ware2->setWareSn('WARE_NAME_2');
        $ware2->setWareName('同名测试货品');

        $ware3 = new StockWare();
        $ware3->setWareSn('WARE_NAME_3');
        $ware3->setWareName('不同名测试货品');

        $repository->save($ware1);
        $repository->save($ware2);
        $repository->save($ware3);

        $sameNameWares = $repository->findByWareName('同名测试货品');
        $this->assertCount(2, $sameNameWares);

        $differentNameWares = $repository->findByWareName('不同名测试货品');
        $this->assertCount(1, $differentNameWares);
        $this->assertSame('WARE_NAME_3', $differentNameWares[0]->getWareSn());

        $noWares = $repository->findByWareName('不存在的货品');
        $this->assertEmpty($noWares);
    }

    public function testFindBySpecification(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('SPEC_WARE');
        $stockWare->setWareName('规格测试货品');
        $stockWare->setSpecification('特殊规格');

        $repository->save($stockWare);

        $foundStockWares = $repository->findBy(['specification' => '特殊规格']);
        $this->assertNotEmpty($foundStockWares);
        $this->assertSame('SPEC_WARE', $foundStockWares[0]->getWareSn());
        $this->assertSame('规格测试货品', $foundStockWares[0]->getWareName());

        $notFoundStockWares = $repository->findBy(['specification' => '不存在的规格']);
        $this->assertEmpty($notFoundStockWares);
    }

    public function testFindByTypeWithMultipleWares(): void
    {
        $repository = self::getService(StockWareRepository::class);

        // 清理所有现有数据
        $allWares = $repository->findAll();
        foreach ($allWares as $ware) {
            $repository->remove($ware);
        }

        $normalWare1 = new StockWare();
        $normalWare1->setWareSn('NORMAL_WARE_1');
        $normalWare1->setWareName('普通货品1');
        $normalWare1->setType(StockWareTypeEnum::NORMAL);

        $normalWare2 = new StockWare();
        $normalWare2->setWareSn('NORMAL_WARE_2');
        $normalWare2->setWareName('普通货品2');
        $normalWare2->setType(StockWareTypeEnum::NORMAL);

        $virtualWare = new StockWare();
        $virtualWare->setWareSn('VIRTUAL_WARE');
        $virtualWare->setWareName('虚拟货品');
        $virtualWare->setType(StockWareTypeEnum::VIRTUAL);

        $repository->save($normalWare1);
        $repository->save($normalWare2);
        $repository->save($virtualWare);

        $normalWares = $repository->findBy(['type' => StockWareTypeEnum::NORMAL]);
        $this->assertCount(2, $normalWares);

        $virtualWares = $repository->findBy(['type' => StockWareTypeEnum::VIRTUAL]);
        $this->assertCount(1, $virtualWares);
        $this->assertSame('虚拟货品', $virtualWares[0]->getWareName());
    }

    public function testFindByBrand(): void
    {
        $repository = self::getService(StockWareRepository::class);

        $brandWare1 = new StockWare();
        $brandWare1->setWareSn('BRAND_WARE_1');
        $brandWare1->setWareName('品牌货品1');
        $brandWare1->setBrand('苹果');

        $brandWare2 = new StockWare();
        $brandWare2->setWareSn('BRAND_WARE_2');
        $brandWare2->setWareName('品牌货品2');
        $brandWare2->setBrand('苹果');

        $otherBrandWare = new StockWare();
        $otherBrandWare->setWareSn('OTHER_BRAND_WARE');
        $otherBrandWare->setWareName('其他品牌货品');
        $otherBrandWare->setBrand('华为');

        $repository->save($brandWare1);
        $repository->save($brandWare2);
        $repository->save($otherBrandWare);

        $appleWares = $repository->findByBrand('苹果');
        $this->assertCount(2, $appleWares);

        $huaweiWares = $repository->findByBrand('华为');
        $this->assertCount(1, $huaweiWares);
        $this->assertSame('其他品牌货品', $huaweiWares[0]->getWareName());

        $samsungWares = $repository->findByBrand('三星');
        $this->assertEmpty($samsungWares);
    }

    public function testFindAllReturnsAllWares(): void
    {
        $repository = self::getService(StockWareRepository::class);

        // 清空现有数据
        $allWares = $repository->findAll();
        foreach ($allWares as $ware) {
            $repository->remove($ware);
        }

        $ware1 = new StockWare();
        $ware1->setWareSn('ALL_WARE_1');
        $ware1->setWareName('全部货品1');

        $ware2 = new StockWare();
        $ware2->setWareSn('ALL_WARE_2');
        $ware2->setWareName('全部货品2');

        $repository->save($ware1);
        $repository->save($ware2);

        $allWares = $repository->findAll();
        $this->assertCount(2, $allWares);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(StockWareRepository::class);

        // 清理现有数据
        $allWares = $repository->findAll();
        foreach ($allWares as $ware) {
            $repository->remove($ware);
        }

        // 创建测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $ware = new StockWare();
            $ware->setWareSn("LIMIT_WARE_{$i}");
            $ware->setWareName("分页货品 {$i}");
            $repository->save($ware);
        }

        $wares = $repository->findBy([], ['wareName' => 'ASC'], 2, 1);
        $this->assertCount(2, $wares);
        $this->assertSame('分页货品 2', $wares[0]->getWareName());
        $this->assertSame('分页货品 3', $wares[1]->getWareName());
    }

    protected function createNewEntity(): StockWare
    {
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE_' . uniqid());
        $stockWare->setWareName('Test Stock Ware ' . uniqid());
        $stockWare->setType(StockWareTypeEnum::NORMAL);

        return $stockWare;
    }

    protected function getRepository(): StockWareRepository
    {
        return self::getService(StockWareRepository::class);
    }

    public function testFindActiveWares(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $allWares = $repository->findAll();
        foreach ($allWares as $ware) {
            $repository->remove($ware);
        }

        // 创建货品（实体无status字段，findActiveWares返回全部）
        $ware1 = new StockWare();
        $ware1->setWareSn('WARE_1');
        $ware1->setWareName('货品1');
        $repository->save($ware1);

        $ware2 = new StockWare();
        $ware2->setWareSn('WARE_2');
        $ware2->setWareName('货品2');
        $repository->save($ware2);

        $activeWares = $repository->findActiveWares();

        $this->assertIsArray($activeWares);
        $this->assertCount(2, $activeWares);
    }

    public function testFindByBarCode(): void
    {
        $repository = $this->getRepository();

        // 实体无barCode字段，方法总是返回null
        $result = $repository->findByBarCode('123456789');
        $this->assertNull($result);

        $notFound = $repository->findByBarCode('999999999');
        $this->assertNull($notFound);
    }
}
