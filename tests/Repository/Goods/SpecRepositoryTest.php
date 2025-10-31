<?php

namespace PinduoduoApiBundle\Tests\Repository\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Spec;
use PinduoduoApiBundle\Repository\Goods\SpecRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SpecRepository::class)]
#[RunTestsInSeparateProcesses]
final class SpecRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(SpecRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            $repository->remove($spec);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $spec = new Spec();
        $spec->setName('Test Spec');

        $repository->save($spec);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(SpecRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindSpec(): void
    {
        $repository = self::getService(SpecRepository::class);

        $spec = new Spec();
        $spec->setName('颜色');

        $repository->save($spec);

        $foundSpec = $repository->find($spec->getId());
        $this->assertNotNull($foundSpec);
        $this->assertSame('颜色', $foundSpec->getName());
    }

    public function testFindOneByName(): void
    {
        $repository = self::getService(SpecRepository::class);

        $spec = new Spec();
        $spec->setName('尺寸');

        $repository->save($spec);

        $foundSpec = $repository->findOneBy(['name' => '尺寸']);
        $this->assertNotNull($foundSpec);
        $this->assertSame('尺寸', $foundSpec->getName());
    }

    public function testSpecCategoryRelationship(): void
    {
        $repository = self::getService(SpecRepository::class);

        $category1 = new Category();
        $category1->setName('服装');
        $category1->setLevel(1);
        self::getEntityManager()->persist($category1);

        $category2 = new Category();
        $category2->setName('数码');
        $category2->setLevel(1);
        self::getEntityManager()->persist($category2);

        $spec = new Spec();
        $spec->setName('材质');
        $spec->addCategory($category1);
        $spec->addCategory($category2);

        $repository->save($spec);

        $foundSpec = $repository->find($spec->getId());
        $this->assertNotNull($foundSpec);
        $this->assertCount(2, $foundSpec->getCategories());

        $categoryNames = [];
        foreach ($foundSpec->getCategories() as $category) {
            $categoryNames[] = $category->getName();
        }
        $this->assertContains('服装', $categoryNames);
        $this->assertContains('数码', $categoryNames);
    }

    public function testFindAllReturnsAllSpecs(): void
    {
        $repository = self::getService(SpecRepository::class);

        // 清空现有数据
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            $repository->remove($spec);
        }

        $spec1 = new Spec();
        $spec1->setName('重量');

        $spec2 = new Spec();
        $spec2->setName('品牌');

        $repository->save($spec1);
        $repository->save($spec2);

        $specs = $repository->findAll();
        $this->assertCount(2, $specs);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(SpecRepository::class);

        // 清理现有数据
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            $repository->remove($spec);
        }

        for ($i = 1; $i <= 5; ++$i) {
            $spec = new Spec();
            $spec->setName("规格 {$i}");
            $repository->save($spec);
        }

        $specs = $repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $specs);
        $this->assertSame('规格 2', $specs[0]->getName());
        $this->assertSame('规格 3', $specs[1]->getName());
    }

    public function testRemoveSpecFromCategory(): void
    {
        $repository = self::getService(SpecRepository::class);

        $category = new Category();
        $category->setName('电子产品');
        $category->setLevel(1);
        self::getEntityManager()->persist($category);

        $spec = new Spec();
        $spec->setName('屏幕尺寸');
        $spec->addCategory($category);

        $repository->save($spec);

        $this->assertCount(1, $spec->getCategories());

        $spec->removeCategory($category);
        $repository->save($spec);

        $foundSpec = $repository->find($spec->getId());
        $this->assertNotNull($foundSpec);
        $this->assertCount(0, $foundSpec->getCategories());
    }

    public function testRemoveSpec(): void
    {
        $repository = self::getService(SpecRepository::class);

        $spec = new Spec();
        $spec->setName('待删除规格');

        $repository->save($spec);
        $id = $spec->getId();

        $repository->remove($spec);

        $foundSpec = $repository->find($id);
        $this->assertNull($foundSpec);
    }

    public function testSpecWithEmptyCategories(): void
    {
        $repository = self::getService(SpecRepository::class);

        $spec = new Spec();
        $spec->setName('独立规格');

        $repository->save($spec);

        $foundSpec = $repository->find($spec->getId());
        $this->assertNotNull($foundSpec);
        $this->assertCount(0, $foundSpec->getCategories());
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(SpecRepository::class);

        $this->clearAllSpecs($repository);

        $spec1 = new Spec();
        $spec1->setName('Spec C');
        $this->persistAndFlush($spec1);

        $spec2 = new Spec();
        $spec2->setName('Spec A');
        $this->persistAndFlush($spec2);

        $spec3 = new Spec();
        $spec3->setName('Spec B');
        $this->persistAndFlush($spec3);

        $firstSpecAsc = $repository->findOneBy([], ['name' => 'ASC']);
        $this->assertNotNull($firstSpecAsc);
        $this->assertSame('Spec A', $firstSpecAsc->getName());

        $firstSpecDesc = $repository->findOneBy([], ['name' => 'DESC']);
        $this->assertNotNull($firstSpecDesc);
        $this->assertSame('Spec C', $firstSpecDesc->getName());

        $newestSpec = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestSpec);
        $this->assertSame($spec3->getId(), $newestSpec->getId());
    }

    private function clearAllSpecs(SpecRepository $repository): void
    {
        $allSpecs = $repository->findAll();
        foreach ($allSpecs as $spec) {
            self::getEntityManager()->remove($spec);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Spec
    {
        $entity = new Spec();
        $entity->setName('Test Spec ' . uniqid());

        return $entity;
    }

    protected function getRepository(): SpecRepository
    {
        return self::getService(SpecRepository::class);
    }
}
