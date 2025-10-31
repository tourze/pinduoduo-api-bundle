<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareSpec::class)]
final class StockWareSpecTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $stockWareSpec = new StockWareSpec();
        $this->assertInstanceOf(StockWareSpec::class, $stockWareSpec);
    }

    public function testToString(): void
    {
        $stockWareSpec = new StockWareSpec();
        $this->assertSame('', $stockWareSpec->__toString());
    }

    protected function createEntity(): object
    {
        return new StockWareSpec();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'specId' => ['specId', '12345'],
            'specKey' => ['specKey', '颜色'],
            'specValue' => ['specValue', '红色'],
        ];
    }
}
