<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AuthCatRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AuthCatRepository::class)]
#[RunTestsInSeparateProcesses]
final class AuthCatRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(AuthCatRepository::class);
        $entityManager = self::getEntityManager();

        // 清理现有数据，避免 DataFixtures 检查失败
        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            $entityManager->remove($authCat);
        }
        $entityManager->flush();

        // 创建关联的 Mall
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for AuthCat');
        $mall->setDescription('Test mall for authcat data fixtures');
        $entityManager->persist($mall);

        // 添加一个测试数据以满足 DataFixtures 检查
        $authCat = new AuthCat();
        $authCat->setMall($mall);
        $authCat->setParentCatId('0');
        $authCat->setCatId('123456');
        $authCat->setCatName('Test Category');
        $authCat->setLeaf(true);

        $entityManager->persist($authCat);
        $entityManager->flush();

        // 清理实体管理器状态以确保连接状态测试正常
        $entityManager->clear();
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindAuthCat(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        // 创建一个 Mall 实体作为关联
        $mall = new Mall();
        $mall->setName('Test Mall');
        $this->persistAndFlush($mall);

        $authCat = new AuthCat();
        $authCat->setMall($mall);
        $authCat->setParentCatId('100');
        $authCat->setCatId('200');
        $authCat->setCatName('Test Category');
        $authCat->setLeaf(true);

        $this->persistAndFlush($authCat);

        $foundAuthCat = $repository->find($authCat->getId());
        $this->assertNotNull($foundAuthCat);
        $this->assertSame('Test Category', $foundAuthCat->getCatName());
        $this->assertSame('200', $foundAuthCat->getCatId());
        $this->assertTrue($foundAuthCat->isLeaf());
    }

    public function testFindByCatId(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        // 创建一个 Mall 实体作为关联
        $mall = new Mall();
        $mall->setName('Another Mall');
        $this->persistAndFlush($mall);

        $authCat = new AuthCat();
        $authCat->setMall($mall);
        $authCat->setParentCatId('300');
        $authCat->setCatId('unique_cat_id');
        $authCat->setCatName('Unique Category');
        $authCat->setLeaf(false);

        $this->persistAndFlush($authCat);

        $foundAuthCat = $repository->findOneBy(['catId' => 'unique_cat_id']);
        $this->assertNotNull($foundAuthCat);
        $this->assertSame('Unique Category', $foundAuthCat->getCatName());
        $this->assertFalse($foundAuthCat->isLeaf());
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        // 创建 Mall 实体
        $mall = new Mall();
        $mall->setName('Mall With Categories');
        $this->persistAndFlush($mall);

        // 创建多个 AuthCat 实体
        $authCat1 = new AuthCat();
        $authCat1->setMall($mall);
        $authCat1->setParentCatId('0');
        $authCat1->setCatId('400');
        $authCat1->setCatName('Category 1');
        $authCat1->setLeaf(true);

        $authCat2 = new AuthCat();
        $authCat2->setMall($mall);
        $authCat2->setParentCatId('400');
        $authCat2->setCatId('401');
        $authCat2->setCatName('Category 2');
        $authCat2->setLeaf(true);

        $this->persistAndFlush($authCat1);
        $this->persistAndFlush($authCat2);

        $authCats = $repository->findBy(['mall' => $mall]);
        $this->assertCount(2, $authCats);
    }

    public function testFindByLeafCategory(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        // 清理现有数据
        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        // 创建 Mall 实体
        $mall = new Mall();
        $mall->setName('Mall For Leaf Test');
        $this->persistAndFlush($mall);

        // 创建叶子类目和非叶子类目
        $leafCat = new AuthCat();
        $leafCat->setMall($mall);
        $leafCat->setParentCatId('0');
        $leafCat->setCatId('500');
        $leafCat->setCatName('Leaf Category');
        $leafCat->setLeaf(true);

        $nonLeafCat = new AuthCat();
        $nonLeafCat->setMall($mall);
        $nonLeafCat->setParentCatId('0');
        $nonLeafCat->setCatId('501');
        $nonLeafCat->setCatName('Non-Leaf Category');
        $nonLeafCat->setLeaf(false);

        $this->persistAndFlush($leafCat);
        $this->persistAndFlush($nonLeafCat);

        $leafCats = $repository->findBy(['leaf' => true]);
        $this->assertCount(1, $leafCats);
        $this->assertSame('Leaf Category', $leafCats[0]->getCatName());
    }

    public function testFindWithParentCatIdIsNull(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        // 创建 Mall 实体
        $mall = new Mall();
        $mall->setName('Mall For Parent Test');
        $this->persistAndFlush($mall);

        // 创建根类目
        $rootCat = new AuthCat();
        $rootCat->setMall($mall);
        $rootCat->setParentCatId('0');
        $rootCat->setCatId('600');
        $rootCat->setCatName('Root Category');
        $rootCat->setLeaf(false);

        $this->persistAndFlush($rootCat);

        $rootCats = $repository->findBy(['parentCatId' => '0']);
        $this->assertGreaterThan(0, count($rootCats));
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        $mall = new Mall();
        $mall->setName('Test Mall For OneBy Order');
        $this->persistAndFlush($mall);

        $authCat1 = new AuthCat();
        $authCat1->setMall($mall);
        $authCat1->setParentCatId('0');
        $authCat1->setCatId('alpha_cat');
        $authCat1->setCatName('Alpha Category');
        $authCat1->setLeaf(true);
        $this->persistAndFlush($authCat1);

        $authCat2 = new AuthCat();
        $authCat2->setMall($mall);
        $authCat2->setParentCatId('0');
        $authCat2->setCatId('beta_cat');
        $authCat2->setCatName('Beta Category');
        $authCat2->setLeaf(false);
        $this->persistAndFlush($authCat2);

        $authCat3 = new AuthCat();
        $authCat3->setMall($mall);
        $authCat3->setParentCatId('0');
        $authCat3->setCatId('gamma_cat');
        $authCat3->setCatName('Gamma Category');
        $authCat3->setLeaf(true);
        $this->persistAndFlush($authCat3);

        $firstAuthCatAsc = $repository->findOneBy([], ['catName' => 'ASC']);
        $this->assertNotNull($firstAuthCatAsc);
        $this->assertSame('Alpha Category', $firstAuthCatAsc->getCatName());

        $firstAuthCatDesc = $repository->findOneBy([], ['catName' => 'DESC']);
        $this->assertNotNull($firstAuthCatDesc);
        $this->assertSame('Gamma Category', $firstAuthCatDesc->getCatName());

        $newestAuthCat = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestAuthCat);
        $this->assertSame($authCat3->getId(), $newestAuthCat->getId());

        $specificAuthCat = $repository->findOneBy(['leaf' => false], ['id' => 'ASC']);
        $this->assertNotNull($specificAuthCat);
        $this->assertSame('Beta Category', $specificAuthCat->getCatName());
        $this->assertSame($authCat2->getId(), $specificAuthCat->getId());
    }

    public function testFindByWithNullCriteria(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        $mall = new Mall();
        $mall->setName('Test Mall For Null Criteria');
        $this->persistAndFlush($mall);

        $authCat1 = new AuthCat();
        $authCat1->setMall($mall);
        $authCat1->setParentCatId('0');
        $authCat1->setCatId('null_test_cat');
        $authCat1->setCatName('Null Test Category');
        $authCat1->setLeaf(null);
        $this->persistAndFlush($authCat1);

        $authCatsWithNullLeaf = $repository->createQueryBuilder('ac')
            ->where('ac.leaf IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($authCatsWithNullLeaf);
        $this->assertGreaterThanOrEqual(1, count($authCatsWithNullLeaf));

        $found = false;
        foreach ($authCatsWithNullLeaf as $authCat) {
            if ($authCat instanceof AuthCat && 'Null Test Category' === $authCat->getCatName()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCountWithNullCriteria(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        $mall = new Mall();
        $mall->setName('Test Mall For Null Count');
        $this->persistAndFlush($mall);

        $authCat1 = new AuthCat();
        $authCat1->setMall($mall);
        $authCat1->setParentCatId('0');
        $authCat1->setCatId('null_count_cat');
        $authCat1->setCatName('Null Count Category');
        $authCat1->setLeaf(null);
        $this->persistAndFlush($authCat1);

        $authCat2 = new AuthCat();
        $authCat2->setMall($mall);
        $authCat2->setParentCatId('0');
        $authCat2->setCatId('leaf_count_cat');
        $authCat2->setCatName('Leaf Count Category');
        $authCat2->setLeaf(true);
        $this->persistAndFlush($authCat2);

        $nullLeafCount = (int) $repository->createQueryBuilder('ac')
            ->select('COUNT(ac.id)')
            ->where('ac.leaf IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(1, $nullLeafCount);

        $nonNullLeafCount = (int) $repository->createQueryBuilder('ac')
            ->select('COUNT(ac.id)')
            ->where('ac.leaf IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(1, $nonNullLeafCount);

        $totalCount = $repository->count([]);
        $this->assertSame(2, $totalCount);
        $this->assertSame($totalCount, $nullLeafCount + $nonNullLeafCount);
    }

    public function testFindByMallAssociation(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        $mall1 = new Mall();
        $mall1->setName('Association Mall 1');
        $this->persistAndFlush($mall1);

        $mall2 = new Mall();
        $mall2->setName('Association Mall 2');
        $this->persistAndFlush($mall2);

        $authCat1 = new AuthCat();
        $authCat1->setMall($mall1);
        $authCat1->setParentCatId('0');
        $authCat1->setCatId('assoc_cat_1');
        $authCat1->setCatName('Association Category 1');
        $authCat1->setLeaf(true);
        $this->persistAndFlush($authCat1);

        $authCat2 = new AuthCat();
        $authCat2->setMall($mall1);
        $authCat2->setParentCatId('0');
        $authCat2->setCatId('assoc_cat_2');
        $authCat2->setCatName('Association Category 2');
        $authCat2->setLeaf(false);
        $this->persistAndFlush($authCat2);

        $authCat3 = new AuthCat();
        $authCat3->setMall($mall2);
        $authCat3->setParentCatId('0');
        $authCat3->setCatId('assoc_cat_3');
        $authCat3->setCatName('Association Category 3');
        $authCat3->setLeaf(true);
        $this->persistAndFlush($authCat3);

        $mall1AuthCats = $repository->findBy(['mall' => $mall1]);
        $this->assertCount(2, $mall1AuthCats);

        $mall2AuthCats = $repository->findBy(['mall' => $mall2]);
        $this->assertCount(1, $mall2AuthCats);
        $this->assertSame('Association Category 3', $mall2AuthCats[0]->getCatName());
    }

    public function testCountWithMallAssociation(): void
    {
        $repository = self::getService(AuthCatRepository::class);

        $allAuthCats = $repository->findAll();
        foreach ($allAuthCats as $authCat) {
            self::getEntityManager()->remove($authCat);
        }
        self::getEntityManager()->flush();

        $mall1 = new Mall();
        $mall1->setName('Count Association Mall 1');
        $this->persistAndFlush($mall1);

        $mall2 = new Mall();
        $mall2->setName('Count Association Mall 2');
        $this->persistAndFlush($mall2);

        for ($i = 1; $i <= 3; ++$i) {
            $authCat = new AuthCat();
            $authCat->setMall($mall1);
            $authCat->setParentCatId('0');
            $authCat->setCatId("count_assoc_cat_{$i}");
            $authCat->setCatName("Count Association Category {$i}");
            $authCat->setLeaf(0 === $i % 2);
            $this->persistAndFlush($authCat);
        }

        $authCat4 = new AuthCat();
        $authCat4->setMall($mall2);
        $authCat4->setParentCatId('0');
        $authCat4->setCatId('count_assoc_cat_4');
        $authCat4->setCatName('Count Association Category 4');
        $authCat4->setLeaf(true);
        $this->persistAndFlush($authCat4);

        $mall1Count = $repository->count(['mall' => $mall1]);
        $this->assertSame(3, $mall1Count);

        $mall2Count = $repository->count(['mall' => $mall2]);
        $this->assertSame(1, $mall2Count);
    }

    protected function createNewEntity(): AuthCat
    {
        $mall = new Mall();
        $mall->setName('Test Mall for AuthCat ' . uniqid());
        $mall->setDescription('Test mall description');

        // 手动持久化 mall 实体，因为关联没有配置级联持久化
        self::getEntityManager()->persist($mall);

        $entity = new AuthCat();
        $entity->setMall($mall);
        $entity->setCatId('test_cat_' . uniqid());
        $entity->setCatName('Test Category ' . uniqid());
        $entity->setParentCatId('0');
        $entity->setLeaf(true);

        return $entity;
    }

    protected function getRepository(): AuthCatRepository
    {
        return self::getService(AuthCatRepository::class);
    }
}
