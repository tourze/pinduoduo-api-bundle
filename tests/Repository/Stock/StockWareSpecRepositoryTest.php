<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;
use PinduoduoApiBundle\Repository\Stock\StockWareSpecRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareSpecRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockWareSpecRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(StockWareSpecRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allStockWareSpecs = $repository->findAll();
        foreach ($allStockWareSpecs as $stockWareSpec) {
            $this->assertInstanceOf(StockWareSpec::class, $stockWareSpec);
            $repository->remove($stockWareSpec);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('123456');
        $stockWareSpec->setSpecKey('TEST_SPEC');
        $stockWareSpec->setSpecValue('Test Value');

        $repository->save($stockWareSpec);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindStockWareSpec(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('save_test_spec');
        $stockWareSpec->setSpecKey('test_key');
        $stockWareSpec->setSpecValue('test_value');
        $stockWareSpec->setParentId('parent_spec');

        $repository->save($stockWareSpec);

        $foundEntity = $repository->find($stockWareSpec->getId());
        $this->assertNotNull($foundEntity);
        $this->assertSame('save_test_spec', $foundEntity->getSpecId());
        $this->assertSame('test_key', $foundEntity->getSpecKey());
        $this->assertSame('test_value', $foundEntity->getSpecValue());
        $this->assertSame('parent_spec', $foundEntity->getParentId());
    }

    public function testFindBySpecId(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('unique_spec_id');
        $stockWareSpec->setSpecKey('unique_key');
        $stockWareSpec->setSpecValue('unique_value');

        $repository->save($stockWareSpec);

        $foundSpec = $repository->findBySpecId('unique_spec_id');
        $this->assertNotNull($foundSpec);
        $this->assertSame('unique_key', $foundSpec->getSpecKey());
        $this->assertSame('unique_value', $foundSpec->getSpecValue());

        $notFoundSpec = $repository->findBySpecId('nonexistent_spec_id');
        $this->assertNull($notFoundSpec);
    }

    public function testFindByStockWareSku(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $spec1 = new StockWareSpec();
        $spec1->setSpecId('spec_1');
        $spec1->setSpecKey('color');
        $spec1->setSpecValue('red');
        $spec1->setStockWareSku(null);

        $spec2 = new StockWareSpec();
        $spec2->setSpecId('spec_2');
        $spec2->setSpecKey('size');
        $spec2->setSpecValue('large');
        $spec2->setStockWareSku(null);

        $spec3 = new StockWareSpec();
        $spec3->setSpecId('spec_3');
        $spec3->setSpecKey('color');
        $spec3->setSpecValue('blue');
        $spec3->setStockWareSku(null);

        $repository->save($spec1);
        $repository->save($spec2);
        $repository->save($spec3);

        $specsForSku123 = $repository->findByStockWareSku('sku_123');
        $this->assertCount(2, $specsForSku123);

        $specsForSku456 = $repository->findByStockWareSku('sku_456');
        $this->assertCount(1, $specsForSku456);
        $this->assertSame('blue', $specsForSku456[0]->getSpecValue());

        $specsForNonexistent = $repository->findByStockWareSku('sku_999');
        $this->assertEmpty($specsForNonexistent);
    }

    public function testFindBySpecKey(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $spec1 = new StockWareSpec();
        $spec1->setSpecId('spec_key_1');
        $spec1->setSpecKey('color');
        $spec1->setSpecValue('red');

        $spec2 = new StockWareSpec();
        $spec2->setSpecId('spec_key_2');
        $spec2->setSpecKey('color');
        $spec2->setSpecValue('blue');

        $spec3 = new StockWareSpec();
        $spec3->setSpecId('spec_key_3');
        $spec3->setSpecKey('size');
        $spec3->setSpecValue('medium');

        $repository->save($spec1);
        $repository->save($spec2);
        $repository->save($spec3);

        $colorSpecs = $repository->findBySpecKey('color');
        $this->assertCount(2, $colorSpecs);

        $sizeSpecs = $repository->findBySpecKey('size');
        $this->assertCount(1, $sizeSpecs);
        $this->assertSame('medium', $sizeSpecs[0]->getSpecValue());

        $brandSpecs = $repository->findBySpecKey('brand');
        $this->assertEmpty($brandSpecs);
    }

    public function testFindAllReturnsAllSpecs(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        // 清空现有数据
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            $repository->remove($spec);
        }

        $spec1 = new StockWareSpec();
        $spec1->setSpecId('all_spec_1');
        $spec1->setSpecKey('all_key_1');
        $spec1->setSpecValue('all_value_1');

        $spec2 = new StockWareSpec();
        $spec2->setSpecId('all_spec_2');
        $spec2->setSpecKey('all_key_2');
        $spec2->setSpecValue('all_value_2');

        $repository->save($spec1);
        $repository->save($spec2);

        $allSpecs = $repository->findAll();
        $this->assertCount(2, $allSpecs);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        // 清理现有数据
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            $repository->remove($spec);
        }

        // 创建测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $spec = new StockWareSpec();
            $spec->setSpecId("limit_spec_{$i}");
            $spec->setSpecKey("limit_key_{$i}");
            $spec->setSpecValue("分页值 {$i}");
            $repository->save($spec);
        }

        $specs = $repository->findBy([], ['specValue' => 'ASC'], 2, 1);
        $this->assertCount(2, $specs);
        $this->assertSame('分页值 2', $specs[0]->getSpecValue());
        $this->assertSame('分页值 3', $specs[1]->getSpecValue());
    }

    public function testRemoveStockWareSpec(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('remove_spec');
        $stockWareSpec->setSpecKey('remove_key');
        $stockWareSpec->setSpecValue('To Be Removed');

        $repository->save($stockWareSpec);
        $id = $stockWareSpec->getId();

        $repository->remove($stockWareSpec);

        $foundEntity = $repository->find($id);
        $this->assertNull($foundEntity);
    }

    public function testFindByWithNullFields(): void
    {
        $repository = self::getService(StockWareSpecRepository::class);

        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('null_test_spec');
        $stockWareSpec->setSpecKey('null_key');
        $stockWareSpec->setSpecValue('null_value');
        $stockWareSpec->setParentId(null);
        $stockWareSpec->setStockWareSku(null);

        $repository->save($stockWareSpec);

        $specsWithNullParent = $repository->findBy(['parentId' => null]);
        $this->assertNotEmpty($specsWithNullParent);

        $specsWithNullSku = $repository->findBy(['stockWareSku' => null]);
        $this->assertNotEmpty($specsWithNullSku);
    }

    protected function createNewEntity(): StockWareSpec
    {
        $stockWareSpec = new StockWareSpec();
        $stockWareSpec->setSpecId('12345' . mt_rand(1000, 9999));
        $stockWareSpec->setSpecKey('TEST_SPEC_' . uniqid());
        $stockWareSpec->setSpecValue('Test Value ' . uniqid());

        return $stockWareSpec;
    }

    protected function getRepository(): StockWareSpecRepository
    {
        return self::getService(StockWareSpecRepository::class);
    }
}
