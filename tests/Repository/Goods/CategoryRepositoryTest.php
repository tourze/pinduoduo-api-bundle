<?php

namespace PinduoduoApiBundle\Tests\Repository\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CategoryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CategoryRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(CategoryRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allCategories = $repository->findAll();
        foreach ($allCategories as $category) {
            $repository->remove($category);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $category = new Category();
        $category->setName('Test Category');
        $category->setLevel(1);

        $repository->save($category);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindCategory(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $category = new Category();
        $category->setName('Test Category');
        $category->setLevel(1);

        $this->persistAndFlush($category);

        $foundCategory = $repository->find($category->getId());
        $this->assertNotNull($foundCategory);
        $this->assertSame('Test Category', $foundCategory->getName());
        $this->assertSame(1, $foundCategory->getLevel());
    }

    public function testFindOneByName(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $category = new Category();
        $category->setName('Unique Category');
        $category->setLevel(2);

        $this->persistAndFlush($category);

        $foundCategory = $repository->findOneBy(['name' => 'Unique Category']);
        $this->assertNotNull($foundCategory);
        $this->assertSame('Unique Category', $foundCategory->getName());
        $this->assertSame(2, $foundCategory->getLevel());
    }

    public function testFindByLevel(): void
    {
        $repository = self::getService(CategoryRepository::class);

        // 清空现有数据
        $allCategories = $repository->findAll();
        foreach ($allCategories as $category) {
            self::getEntityManager()->remove($category);
        }

        // 创建测试数据
        $category1 = new Category();
        $category1->setName('Level 1 Category 1');
        $category1->setLevel(1);

        $category2 = new Category();
        $category2->setName('Level 1 Category 2');
        $category2->setLevel(1);

        $category3 = new Category();
        $category3->setName('Level 2 Category');
        $category3->setLevel(2);

        $repository->save($category1);
        $repository->save($category2);
        $repository->save($category3);

        $level1Categories = $repository->findBy(['level' => 1]);
        $this->assertCount(2, $level1Categories);

        $level2Categories = $repository->findBy(['level' => 2]);
        $this->assertCount(1, $level2Categories);
    }

    public function testFindAllReturnsAllCategories(): void
    {
        $repository = self::getService(CategoryRepository::class);

        // 清空现有数据
        $allCategories = $repository->findAll();
        foreach ($allCategories as $category) {
            self::getEntityManager()->remove($category);
        }

        // 创建测试数据
        $category1 = new Category();
        $category1->setName('Category 1');
        $category1->setLevel(1);

        $category2 = new Category();
        $category2->setName('Category 2');
        $category2->setLevel(2);

        $repository->save($category1);
        $repository->save($category2);

        $categories = $repository->findAll();
        $this->assertCount(2, $categories);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(CategoryRepository::class);

        // 清理现有数据
        $allCategories = $repository->findAll();
        foreach ($allCategories as $category) {
            $repository->remove($category);
        }

        // 创建测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $category = new Category();
            $category->setName("Category {$i}");
            $category->setLevel($i);
            $this->persistAndFlush($category);
        }

        $categories = $repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $categories);
        $this->assertSame('Category 2', $categories[0]->getName());
        $this->assertSame('Category 3', $categories[1]->getName());
    }

    public function testFindByWithNullParent(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $category = new Category();
        $category->setName('Root Category');
        $category->setLevel(1);
        // parent 字段为 null

        $this->persistAndFlush($category);

        $rootCategories = $repository->findBy(['parent' => null]);
        $this->assertNotEmpty($rootCategories);

        $found = false;
        foreach ($rootCategories as $rootCategory) {
            if ('Root Category' === $rootCategory->getName()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testParentChildRelationship(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $parentCategory = new Category();
        $parentCategory->setName('Parent Category');
        $parentCategory->setLevel(1);

        $childCategory = new Category();
        $childCategory->setName('Child Category');
        $childCategory->setLevel(2);
        $childCategory->setParent($parentCategory);

        $repository->save($parentCategory);
        $repository->save($childCategory);

        $foundChild = $repository->find($childCategory->getId());
        $this->assertNotNull($foundChild);
        $this->assertNotNull($foundChild->getParent());
        $this->assertSame('Parent Category', $foundChild->getParent()->getName());
    }

    public function testRemoveCategory(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $category = new Category();
        $category->setName('To Be Removed');
        $category->setLevel(1);

        $this->persistAndFlush($category);
        $id = $category->getId();

        self::getEntityManager()->remove($category);
        self::getEntityManager()->flush();

        $foundCategory = $repository->find($id);
        $this->assertNull($foundCategory);
    }

    public function testFindByWithNullCatRule(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $category = new Category();
        $category->setName('Category with Null Rule');
        $category->setLevel(1);
        $category->setCatRule(null);

        $this->persistAndFlush($category);

        $categoriesWithNullRule = $repository->findBy(['catRule' => null]);
        $this->assertNotEmpty($categoriesWithNullRule);

        $found = false;
        foreach ($categoriesWithNullRule as $cat) {
            if ('Category with Null Rule' === $cat->getName()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(CategoryRepository::class);

        $this->clearAllCategories($repository);

        $category1 = new Category();
        $category1->setName('Category A');
        $category1->setLevel(1);
        $this->persistAndFlush($category1);

        $category2 = new Category();
        $category2->setName('Category B');
        $category2->setLevel(2);
        $this->persistAndFlush($category2);

        $category3 = new Category();
        $category3->setName('Category C');
        $category3->setLevel(3);
        $this->persistAndFlush($category3);

        $firstCategoryAsc = $repository->findOneBy([], ['name' => 'ASC']);
        $this->assertNotNull($firstCategoryAsc);
        $this->assertSame('Category A', $firstCategoryAsc->getName());

        $firstCategoryDesc = $repository->findOneBy([], ['name' => 'DESC']);
        $this->assertNotNull($firstCategoryDesc);
        $this->assertSame('Category C', $firstCategoryDesc->getName());

        $newestCategory = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestCategory);
        $this->assertSame($category3->getId(), $newestCategory->getId());
    }

    private function clearAllCategories(CategoryRepository $repository): void
    {
        $allCategories = $repository->findAll();
        foreach ($allCategories as $category) {
            self::getEntityManager()->remove($category);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Category
    {
        $entity = new Category();
        $entity->setName('Test Category ' . uniqid());
        $entity->setLevel(1);

        return $entity;
    }

    protected function getRepository(): CategoryRepository
    {
        return self::getService(CategoryRepository::class);
    }
}
