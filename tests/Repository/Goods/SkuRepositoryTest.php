<?php

namespace PinduoduoApiBundle\Tests\Repository\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SkuRepository::class)]
#[RunTestsInSeparateProcesses]
final class SkuRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(SkuRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allSkus = $repository->findAll();
        foreach ($allSkus as $sku) {
            $repository->remove($sku);
        }

        // 创建关联的 Mall, Category, Goods 并持久化它们
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for Sku');
        $mall->setDescription('Test mall for sku data fixtures');
        $mall->setMerchantType(MerchantType::企业);
        $mall->setMallCharacter(MallCharacter::NEITHER);
        $mall->setCpsProtocolStatus(true);

        $category = new Category();
        $category->setName('Test Category for Sku');
        $category->setLevel(1);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setCategory($category);
        $goods->setGoodsName('Test Goods for Sku');
        $goods->setGoodsQuantity(100);

        // 持久化所有关联实体
        self::getEntityManager()->persist($mall);
        self::getEntityManager()->persist($category);
        self::getEntityManager()->persist($goods);

        // 添加一个测试数据以满足 DataFixtures 检查
        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setSpecName('Test Sku');
        $sku->setPrice(10000);

        $repository->save($sku);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(SkuRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindSku(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setOuterSkuId('test_sku_id');
        $sku->setSpecName('测试规格');
        $sku->setQuantity(100);
        $sku->setPrice(9999);

        $repository->save($sku);

        $foundSku = $repository->find($sku->getId());
        $this->assertNotNull($foundSku);
        $this->assertSame('test_sku_id', $foundSku->getOuterSkuId());
        $this->assertSame('测试规格', $foundSku->getSpecName());
        $this->assertSame(100, $foundSku->getQuantity());
        $this->assertSame(9999, $foundSku->getPrice());
    }

    public function testFindOneByOuterSkuId(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setOuterSkuId('unique_sku_id');
        $sku->setSpecName('唯一规格');

        $repository->save($sku);

        $foundSku = $repository->findOneBy(['outerSkuId' => 'unique_sku_id']);
        $this->assertNotNull($foundSku);
        $this->assertSame('unique_sku_id', $foundSku->getOuterSkuId());
        $this->assertSame('唯一规格', $foundSku->getSpecName());
    }

    public function testFindByGoods(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods1 = new Goods();
        $goods1->setMall($mall);
        $goods1->setOuterGoodsId('goods_1');
        self::getEntityManager()->persist($goods1);

        $goods2 = new Goods();
        $goods2->setMall($mall);
        $goods2->setOuterGoodsId('goods_2');
        self::getEntityManager()->persist($goods2);

        $sku1 = new Sku();
        $sku1->setGoods($goods1);
        $sku1->setOuterSkuId('sku_1');

        $sku2 = new Sku();
        $sku2->setGoods($goods1);
        $sku2->setOuterSkuId('sku_2');

        $sku3 = new Sku();
        $sku3->setGoods($goods2);
        $sku3->setOuterSkuId('sku_3');

        $repository->save($sku1);
        $repository->save($sku2);
        $repository->save($sku3);

        $goods1Skus = $repository->findBy(['goods' => $goods1]);
        $this->assertCount(2, $goods1Skus);

        $goods2Skus = $repository->findBy(['goods' => $goods2]);
        $this->assertCount(1, $goods2Skus);
    }

    public function testFindByOnsale(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku1 = new Sku();
        $sku1->setGoods($goods);
        $sku1->setOuterSkuId('sku_1');
        $sku1->setOnsale(true);

        $sku2 = new Sku();
        $sku2->setGoods($goods);
        $sku2->setOuterSkuId('sku_2');
        $sku2->setOnsale(false);

        $repository->save($sku1);
        $repository->save($sku2);

        $onsaleSkus = $repository->findBy(['onsale' => true]);
        $this->assertNotEmpty($onsaleSkus);

        $offsaleSkus = $repository->findBy(['onsale' => false]);
        $this->assertNotEmpty($offsaleSkus);
    }

    public function testFindAllReturnsAllSkus(): void
    {
        $repository = self::getService(SkuRepository::class);

        // 清空现有数据
        $allSkus = $repository->findAll();
        foreach ($allSkus as $sku) {
            $repository->remove($sku);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku1 = new Sku();
        $sku1->setGoods($goods);
        $sku1->setOuterSkuId('sku_1');

        $sku2 = new Sku();
        $sku2->setGoods($goods);
        $sku2->setOuterSkuId('sku_2');

        $repository->save($sku1);
        $repository->save($sku2);

        $skus = $repository->findAll();
        $this->assertCount(2, $skus);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(SkuRepository::class);

        // 清理现有数据
        $allSkus = $repository->findAll();
        foreach ($allSkus as $sku) {
            $repository->remove($sku);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        for ($i = 1; $i <= 5; ++$i) {
            $sku = new Sku();
            $sku->setGoods($goods);
            $sku->setOuterSkuId("sku_{$i}");
            $sku->setSpecName("规格 {$i}");
            $repository->save($sku);
        }

        $skus = $repository->findBy([], ['specName' => 'ASC'], 2, 1);
        $this->assertCount(2, $skus);
        $this->assertSame('规格 2', $skus[0]->getSpecName());
        $this->assertSame('规格 3', $skus[1]->getSpecName());
    }

    public function testFindByWithNullFields(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setOuterSkuId('null_fields_sku');
        $sku->setOnsale(null);
        $sku->setQuantity(null);
        $sku->setPrice(null);
        $sku->setSpecName(null);

        $repository->save($sku);

        $skusWithNullOnsale = $repository->findBy(['onsale' => null]);
        $this->assertNotEmpty($skusWithNullOnsale);

        $skusWithNullQuantity = $repository->findBy(['quantity' => null]);
        $this->assertNotEmpty($skusWithNullQuantity);

        $skusWithNullPrice = $repository->findBy(['price' => null]);
        $this->assertNotEmpty($skusWithNullPrice);

        $skusWithNullSpecName = $repository->findBy(['specName' => null]);
        $this->assertNotEmpty($skusWithNullSpecName);
    }

    public function testRemoveSku(): void
    {
        $repository = self::getService(SkuRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setOuterSkuId('to_be_removed');

        $repository->save($sku);
        $id = $sku->getId();

        $repository->remove($sku);

        $foundSku = $repository->find($id);
        $this->assertNull($foundSku);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(SkuRepository::class);

        $this->clearAllSkus($repository);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods');
        self::getEntityManager()->persist($goods);

        $sku1 = new Sku();
        $sku1->setGoods($goods);
        $sku1->setOuterSkuId('sku_c');
        $sku1->setSpecName('Spec C');
        $sku1->setQuantity(30);
        $sku1->setPrice(3000);
        $this->persistAndFlush($sku1);

        $sku2 = new Sku();
        $sku2->setGoods($goods);
        $sku2->setOuterSkuId('sku_a');
        $sku2->setSpecName('Spec A');
        $sku2->setQuantity(10);
        $sku2->setPrice(1000);
        $this->persistAndFlush($sku2);

        $sku3 = new Sku();
        $sku3->setGoods($goods);
        $sku3->setOuterSkuId('sku_b');
        $sku3->setSpecName('Spec B');
        $sku3->setQuantity(20);
        $sku3->setPrice(2000);
        $this->persistAndFlush($sku3);

        $firstSkuAsc = $repository->findOneBy([], ['specName' => 'ASC']);
        $this->assertNotNull($firstSkuAsc);
        $this->assertSame('Spec A', $firstSkuAsc->getSpecName());

        $firstSkuDesc = $repository->findOneBy([], ['specName' => 'DESC']);
        $this->assertNotNull($firstSkuDesc);
        $this->assertSame('Spec C', $firstSkuDesc->getSpecName());

        $lowestPriceSku = $repository->findOneBy([], ['price' => 'ASC']);
        $this->assertNotNull($lowestPriceSku);
        $this->assertSame(1000, $lowestPriceSku->getPrice());

        $highestPriceSku = $repository->findOneBy([], ['price' => 'DESC']);
        $this->assertNotNull($highestPriceSku);
        $this->assertSame(3000, $highestPriceSku->getPrice());

        $newestSku = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestSku);
        $this->assertSame($sku3->getId(), $newestSku->getId());
    }

    private function clearAllSkus(SkuRepository $repository): void
    {
        $allSkus = $repository->findAll();
        foreach ($allSkus as $sku) {
            self::getEntityManager()->remove($sku);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Sku
    {
        // 创建 Mall 对象，但不持久化
        $mall = new Mall();
        $mall->setName('Test Mall for Sku ' . uniqid());
        $mall->setDescription('Test Mall Description');
        $mall->setMerchantType(MerchantType::企业);
        $mall->setMallCharacter(MallCharacter::NEITHER);
        $mall->setCpsProtocolStatus(true);

        // 创建 Goods 对象，但不持久化
        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_goods_' . uniqid());
        $goods->setGoodsName('Test Goods ' . uniqid());

        $entity = new Sku();
        $entity->setGoods($goods);
        $entity->setOuterSkuId('test_sku_' . uniqid());
        $entity->setSpecName('Test Spec ' . uniqid());
        $entity->setQuantity(100);
        $entity->setPrice(9999);
        $entity->setOnsale(true);

        return $entity;
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityShouldPersistedSuccessWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $goods = $entity->getGoods();
        if (null !== $goods) {
            $mall = $goods->getMall();
            if (null !== $mall) {
                $entityManager->persist($mall);
            }
            $entityManager->persist($goods);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityAndDetachShouldNotInIdentityMapWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $goods = $entity->getGoods();
        if (null !== $goods) {
            $mall = $goods->getMall();
            if (null !== $mall) {
                $entityManager->persist($mall);
            }
            $entityManager->persist($goods);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));

        $entityManager->detach($entity);
        $this->assertFalse($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    protected function getRepository(): SkuRepository
    {
        return self::getService(SkuRepository::class);
    }
}
