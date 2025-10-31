<?php

namespace PinduoduoApiBundle\Tests\Entity\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Goods\Category;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Category::class)]
final class CategoryTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $category = new Category();
        $this->assertInstanceOf(Category::class, $category);
    }

    public function testParentChildRelationship(): void
    {
        $parent = new Category();
        $child = new Category();
        $this->assertNull($child->getParent());
        $this->assertCount(0, $parent->getChildren());
        $child->setParent($parent);
        $this->assertSame($parent, $child->getParent());
        $parent->addChild($child);
        $this->assertCount(1, $parent->getChildren());
        $this->assertTrue($parent->getChildren()->contains($child));
        $parent->removeChild($child);
        $this->assertCount(0, $parent->getChildren());
        $this->assertFalse($parent->getChildren()->contains($child));
    }

    public function testToString(): void
    {
        $category = new Category();
        $this->assertSame('', $category->__toString());
    }

    protected function createEntity(): object
    {
        return new Category();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'name' => ['name', '测试分类名称'],
            'level' => ['level', 1],
            'catRule' => ['catRule', [
                'rule1' => 'value1',
                'rule2' => 'value2',
            ]],
        ];
    }
}
