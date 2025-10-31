<?php

namespace PinduoduoApiBundle\Tests\Entity\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Goods\Spec;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Spec::class)]
final class SpecTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $spec = new Spec();
        $this->assertInstanceOf(Spec::class, $spec);
    }

    public function testToString(): void
    {
        $spec = new Spec();
        $this->assertSame('', $spec->__toString());
    }

    protected function createEntity(): object
    {
        return new Spec();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'name' => ['name', '颜色规格'],
        ];
    }
}
