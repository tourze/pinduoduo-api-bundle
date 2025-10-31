<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareSkuRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockWareSkuRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(StockWareSkuRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allStockWareSkus = $repository->findAll();
        foreach ($allStockWareSkus as $stockWareSku) {
            $this->assertInstanceOf(StockWareSku::class, $stockWareSku);
            $repository->remove($stockWareSku);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        // 创建依赖对象
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE');
        $stockWare->setWareName('Test Ware');
        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('123456');
        $stockWareSku->setSkuId('789012');
        $stockWareSku->setSkuName('Test SKU');
        $stockWareSku->setQuantity(50);

        $repository->save($stockWareSku);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindStockWareSku(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('WARE_TEST');
        $stockWare->setWareName('Test Ware for SKU');
        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('test_goods_id');
        $stockWareSku->setSkuId('test_sku_id');
        $stockWareSku->setSkuName('Test SKU Name');
        $stockWareSku->setQuantity(100);

        $repository->save($stockWareSku);

        $foundEntity = $repository->find($stockWareSku->getId());
        $this->assertNotNull($foundEntity);
        $this->assertSame('test_goods_id', $foundEntity->getGoodsId());
        $this->assertSame('test_sku_id', $foundEntity->getSkuId());
        $this->assertSame('Test SKU Name', $foundEntity->getSkuName());
        $this->assertSame(100, $foundEntity->getQuantity());
    }

    public function testFindByGoodsId(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare1 = new StockWare();
        $stockWare1->setWareSn('WARE1');
        $stockWare1->setWareName('Test Ware 1');
        self::getEntityManager()->persist($stockWare1);

        $stockWare2 = new StockWare();
        $stockWare2->setWareSn('WARE2');
        $stockWare2->setWareName('Test Ware 2');
        self::getEntityManager()->persist($stockWare2);

        $sku1 = new StockWareSku();
        $sku1->setStockWare($stockWare1);
        $sku1->setGoodsId('goods_123');
        $sku1->setSkuId('sku_1');
        $sku1->setSkuName('SKU 1');
        $sku1->setQuantity(50);

        $sku2 = new StockWareSku();
        $sku2->setStockWare($stockWare1);
        $sku2->setGoodsId('goods_123');
        $sku2->setSkuId('sku_2');
        $sku2->setSkuName('SKU 2');
        $sku2->setQuantity(75);

        $sku3 = new StockWareSku();
        $sku3->setStockWare($stockWare2);
        $sku3->setGoodsId('goods_456');
        $sku3->setSkuId('sku_3');
        $sku3->setSkuName('SKU 3');
        $sku3->setQuantity(25);

        $repository->save($sku1);
        $repository->save($sku2);
        $repository->save($sku3);

        $skusForGoods123 = $repository->findByGoodsId('goods_123');
        $this->assertCount(2, $skusForGoods123);

        $skusForGoods456 = $repository->findByGoodsId('goods_456');
        $this->assertCount(1, $skusForGoods456);
        $this->assertSame('SKU 3', $skusForGoods456[0]->getSkuName());
    }

    public function testFindBySkuId(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('UNIQUE_WARE');
        $stockWare->setWareName('Unique Ware');
        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('unique_goods');
        $stockWareSku->setSkuId('unique_sku_id');
        $stockWareSku->setSkuName('Unique SKU');
        $stockWareSku->setQuantity(30);

        $repository->save($stockWareSku);

        $foundSku = $repository->findBySkuId('unique_sku_id');
        $this->assertNotNull($foundSku);
        $this->assertSame('Unique SKU', $foundSku->getSkuName());
        $this->assertSame(30, $foundSku->getQuantity());

        $notFoundSku = $repository->findBySkuId('nonexistent_sku');
        $this->assertNull($notFoundSku);
    }

    public function testFindByGoodsIdAndSkuId(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('COMBO_WARE');
        $stockWare->setWareName('Combo Ware');
        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('combo_goods');
        $stockWareSku->setSkuId('combo_sku');
        $stockWareSku->setSkuName('Combo SKU');
        $stockWareSku->setQuantity(40);

        $repository->save($stockWareSku);

        $foundSku = $repository->findByGoodsIdAndSkuId('combo_goods', 'combo_sku');
        $this->assertNotNull($foundSku);
        $this->assertSame('Combo SKU', $foundSku->getSkuName());

        $notFoundSku = $repository->findByGoodsIdAndSkuId('combo_goods', 'wrong_sku');
        $this->assertNull($notFoundSku);

        $notFoundSku2 = $repository->findByGoodsIdAndSkuId('wrong_goods', 'combo_sku');
        $this->assertNull($notFoundSku2);
    }

    public function testFindActiveSkus(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('ACTIVE_WARE');
        $stockWare->setWareName('Active Ware');
        self::getEntityManager()->persist($stockWare);

        $activeSku1 = new StockWareSku();
        $activeSku1->setStockWare($stockWare);
        $activeSku1->setGoodsId('active_goods_1');
        $activeSku1->setSkuId('active_sku_1');
        $activeSku1->setSkuName('Active SKU 1');
        $activeSku1->setQuantity(10);
        $activeSku1->setStatus(1);

        $activeSku2 = new StockWareSku();
        $activeSku2->setStockWare($stockWare);
        $activeSku2->setGoodsId('active_goods_2');
        $activeSku2->setSkuId('active_sku_2');
        $activeSku2->setSkuName('Active SKU 2');
        $activeSku2->setQuantity(20);
        $activeSku2->setStatus(1);

        $inactiveSku = new StockWareSku();
        $inactiveSku->setStockWare($stockWare);
        $inactiveSku->setGoodsId('inactive_goods');
        $inactiveSku->setSkuId('inactive_sku');
        $inactiveSku->setSkuName('Inactive SKU');
        $inactiveSku->setQuantity(15);
        $inactiveSku->setStatus(0);

        $repository->save($activeSku1);
        $repository->save($activeSku2);
        $repository->save($inactiveSku);

        $activeSkus = $repository->findActiveSkus();
        $this->assertCount(2, $activeSkus);

        $skuNames = array_map(fn (StockWareSku $sku) => $sku->getSkuName(), $activeSkus);
        $this->assertContains('Active SKU 1', $skuNames);
        $this->assertContains('Active SKU 2', $skuNames);
        $this->assertNotContains('Inactive SKU', $skuNames);
    }

    public function testFindByStockWareId(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare1 = new StockWare();
        $stockWare1->setWareSn('STOCKWARE_1');
        $stockWare1->setWareName('Stock Ware 1');
        self::getEntityManager()->persist($stockWare1);

        $stockWare2 = new StockWare();
        $stockWare2->setWareSn('STOCKWARE_2');
        $stockWare2->setWareName('Stock Ware 2');
        self::getEntityManager()->persist($stockWare2);

        $sku1 = new StockWareSku();
        $sku1->setStockWare($stockWare1);
        $sku1->setGoodsId('goods_1');
        $sku1->setSkuId('sku_1');
        $sku1->setSkuName('SKU for Ware 1');
        $sku1->setQuantity(60);

        $sku2 = new StockWareSku();
        $sku2->setStockWare($stockWare1);
        $sku2->setGoodsId('goods_2');
        $sku2->setSkuId('sku_2');
        $sku2->setSkuName('Another SKU for Ware 1');
        $sku2->setQuantity(70);

        $sku3 = new StockWareSku();
        $sku3->setStockWare($stockWare2);
        $sku3->setGoodsId('goods_3');
        $sku3->setSkuId('sku_3');
        $sku3->setSkuName('SKU for Ware 2');
        $sku3->setQuantity(80);

        $repository->save($sku1);
        $repository->save($sku2);
        $repository->save($sku3);

        self::getEntityManager()->flush();
        self::getEntityManager()->clear();

        $skusForWare1 = $repository->findByStockWareId((string) $stockWare1->getId());
        $this->assertCount(2, $skusForWare1);

        $skusForWare2 = $repository->findByStockWareId((string) $stockWare2->getId());
        $this->assertCount(1, $skusForWare2);
        $this->assertSame('SKU for Ware 2', $skusForWare2[0]->getSkuName());
    }

    public function testRemoveStockWareSku(): void
    {
        $repository = self::getService(StockWareSkuRepository::class);

        $stockWare = new StockWare();
        $stockWare->setWareSn('REMOVE_WARE');
        $stockWare->setWareName('Remove Ware');
        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('remove_goods');
        $stockWareSku->setSkuId('remove_sku');
        $stockWareSku->setSkuName('To Be Removed');
        $stockWareSku->setQuantity(90);

        $repository->save($stockWareSku);
        $id = $stockWareSku->getId();

        $repository->remove($stockWareSku);

        $foundEntity = $repository->find($id);
        $this->assertNull($foundEntity);
    }

    protected function createNewEntity(): StockWareSku
    {
        $stockWare = new StockWare();
        $stockWare->setWareSn('TEST_WARE_' . uniqid());
        $stockWare->setWareName('Test Ware ' . uniqid());

        self::getEntityManager()->persist($stockWare);

        $stockWareSku = new StockWareSku();
        $stockWareSku->setStockWare($stockWare);
        $stockWareSku->setGoodsId('12345' . mt_rand(1000, 9999));
        $stockWareSku->setSkuId('67890' . mt_rand(1000, 9999));
        $stockWareSku->setSkuName('Test SKU ' . uniqid());
        $stockWareSku->setQuantity(50);

        return $stockWareSku;
    }

    protected function getRepository(): StockWareSkuRepository
    {
        return self::getService(StockWareSkuRepository::class);
    }
}
