<?php

namespace PinduoduoApiBundle\Tests\Repository\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsRepository::class)]
#[RunTestsInSeparateProcesses]
final class GoodsRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(GoodsRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allGoods = $repository->findAll();
        foreach ($allGoods as $goods) {
            $repository->remove($goods);
        }

        // 创建关联的 Mall 和 Category
        $entityManager = self::getEntityManager();

        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for Goods');
        $mall->setDescription('Test mall for goods data fixtures');
        $entityManager->persist($mall);

        $category = new Category();
        $category->setName('Test Category for Goods');
        $category->setLevel(1);
        $entityManager->persist($category);

        $entityManager->flush();

        // 添加一个测试数据以满足 DataFixtures 检查
        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setCategory($category);
        $goods->setGoodsName('Test Goods');
        $goods->setGoodsQuantity(100);
        $goods->setOnsale(true);

        $repository->save($goods);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindGoods(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('test_outer_id');
        $goods->setGoodsSn('test_goods_sn');

        $this->persistAndFlush($goods);

        $foundGoods = $repository->find($goods->getId());
        $this->assertNotNull($foundGoods);
        $this->assertSame('test_outer_id', $foundGoods->getOuterGoodsId());
        $this->assertSame('test_goods_sn', $foundGoods->getGoodsSn());
        $mallFromGoods = $foundGoods->getMall();
        $this->assertNotNull($mallFromGoods);
        $this->assertSame($mall->getId(), $mallFromGoods->getId());
    }

    public function testFindOneByOuterGoodsId(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('unique_outer_id');
        $goods->setGoodsSn('unique_goods_sn');

        $this->persistAndFlush($goods);

        $foundGoods = $repository->findOneBy(['outerGoodsId' => 'unique_outer_id']);
        $this->assertNotNull($foundGoods);
        $this->assertSame('unique_outer_id', $foundGoods->getOuterGoodsId());
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall1 = new Mall();
        $mall1->setName('Mall 1');
        self::getEntityManager()->persist($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall 2');
        self::getEntityManager()->persist($mall2);

        $goods1 = new Goods();
        $goods1->setMall($mall1);
        $goods1->setOuterGoodsId('goods_1');

        $goods2 = new Goods();
        $goods2->setMall($mall1);
        $goods2->setOuterGoodsId('goods_2');

        $goods3 = new Goods();
        $goods3->setMall($mall2);
        $goods3->setOuterGoodsId('goods_3');

        $repository->save($goods1);
        $repository->save($goods2);
        $repository->save($goods3);

        $mall1Goods = $repository->findBy(['mall' => $mall1]);
        $this->assertCount(2, $mall1Goods);

        $mall2Goods = $repository->findBy(['mall' => $mall2]);
        $this->assertCount(1, $mall2Goods);
    }

    public function testFindByCategory(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $category1 = new Category();
        $category1->setName('Category 1');
        $category1->setLevel(1);
        self::getEntityManager()->persist($category1);

        $category2 = new Category();
        $category2->setName('Category 2');
        $category2->setLevel(1);
        self::getEntityManager()->persist($category2);

        $goods1 = new Goods();
        $goods1->setMall($mall);
        $goods1->setCategory($category1);
        $goods1->setOuterGoodsId('goods_1');

        $goods2 = new Goods();
        $goods2->setMall($mall);
        $goods2->setCategory($category1);
        $goods2->setOuterGoodsId('goods_2');

        $goods3 = new Goods();
        $goods3->setMall($mall);
        $goods3->setCategory($category2);
        $goods3->setOuterGoodsId('goods_3');

        $repository->save($goods1);
        $repository->save($goods2);
        $repository->save($goods3);

        $category1Goods = $repository->findBy(['category' => $category1]);
        $this->assertCount(2, $category1Goods);

        $category2Goods = $repository->findBy(['category' => $category2]);
        $this->assertCount(1, $category2Goods);
    }

    public function testFindAllReturnsAllGoods(): void
    {
        $repository = self::getService(GoodsRepository::class);

        // 清空现有数据
        $allGoods = $repository->findAll();
        foreach ($allGoods as $goods) {
            self::getEntityManager()->remove($goods);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods1 = new Goods();
        $goods1->setMall($mall);
        $goods1->setOuterGoodsId('goods_1');

        $goods2 = new Goods();
        $goods2->setMall($mall);
        $goods2->setOuterGoodsId('goods_2');

        $repository->save($goods1);
        $repository->save($goods2);

        $goods = $repository->findAll();
        $this->assertCount(2, $goods);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(GoodsRepository::class);

        // 清理现有数据
        $allGoods = $repository->findAll();
        foreach ($allGoods as $goods) {
            $repository->remove($goods);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        for ($i = 1; $i <= 5; ++$i) {
            $goods = new Goods();
            $goods->setMall($mall);
            $goods->setOuterGoodsId("goods_{$i}");
            $goods->setGoodsSn("sn_{$i}");
            $this->persistAndFlush($goods);
        }

        $goods = $repository->findBy([], ['goodsSn' => 'ASC'], 2, 1);
        $this->assertCount(2, $goods);
        $this->assertSame('sn_2', $goods[0]->getGoodsSn());
        $this->assertSame('sn_3', $goods[1]->getGoodsSn());
    }

    public function testFindByWithNullCategory(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('goods_without_category');
        // category 字段为 null

        $this->persistAndFlush($goods);

        $goodsWithoutCategory = $repository->findBy(['category' => null]);
        $this->assertNotEmpty($goodsWithoutCategory);

        $found = false;
        foreach ($goodsWithoutCategory as $item) {
            if ('goods_without_category' === $item->getOuterGoodsId()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByWithNullRefundable(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('goods_null_refundable');
        $goods->setRefundable(null);

        $this->persistAndFlush($goods);

        $goodsWithNullRefundable = $repository->findBy(['refundable' => null]);
        $this->assertNotEmpty($goodsWithNullRefundable);

        $found = false;
        foreach ($goodsWithNullRefundable as $item) {
            if ('goods_null_refundable' === $item->getOuterGoodsId()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRemoveGoods(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $goods = new Goods();
        $goods->setMall($mall);
        $goods->setOuterGoodsId('to_be_removed');

        $this->persistAndFlush($goods);
        $id = $goods->getId();

        self::getEntityManager()->remove($goods);
        self::getEntityManager()->flush();

        $foundGoods = $repository->find($id);
        $this->assertNull($foundGoods);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(GoodsRepository::class);

        $this->clearAllGoods($repository);

        $mall = new Mall();
        $mall->setName('Test Mall for OneByOrder');
        $this->persistAndFlush($mall);

        $goods1 = new Goods();
        $goods1->setMall($mall);
        $goods1->setOuterGoodsId('goods_a');
        $goods1->setGoodsSn('sn_a');
        $this->persistAndFlush($goods1);

        $goods2 = new Goods();
        $goods2->setMall($mall);
        $goods2->setOuterGoodsId('goods_b');
        $goods2->setGoodsSn('sn_b');
        $this->persistAndFlush($goods2);

        $goods3 = new Goods();
        $goods3->setMall($mall);
        $goods3->setOuterGoodsId('goods_c');
        $goods3->setGoodsSn('sn_c');
        $this->persistAndFlush($goods3);

        $firstGoodsAsc = $repository->findOneBy([], ['outerGoodsId' => 'ASC']);
        $this->assertNotNull($firstGoodsAsc);
        $this->assertSame('goods_a', $firstGoodsAsc->getOuterGoodsId());

        $firstGoodsDesc = $repository->findOneBy([], ['outerGoodsId' => 'DESC']);
        $this->assertNotNull($firstGoodsDesc);
        $this->assertSame('goods_c', $firstGoodsDesc->getOuterGoodsId());

        $newestGoods = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestGoods);
        $this->assertSame($goods3->getId(), $newestGoods->getId());
    }

    private function clearAllGoods(GoodsRepository $repository): void
    {
        $allGoods = $repository->findAll();
        foreach ($allGoods as $goods) {
            self::getEntityManager()->remove($goods);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Goods
    {
        $mall = new Mall();
        $mall->setName('Test Mall ' . uniqid());
        self::getEntityManager()->persist($mall);

        $entity = new Goods();
        $entity->setMall($mall);
        $entity->setOuterGoodsId('test_goods_' . uniqid());
        $entity->setGoodsSn('test_sn_' . uniqid());

        return $entity;
    }

    protected function getRepository(): GoodsRepository
    {
        return self::getService(GoodsRepository::class);
    }
}
