<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareSku::class)]
final class StockWareSkuTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $stockWareSku = new StockWareSku();
        $this->assertInstanceOf(StockWareSku::class, $stockWareSku);
    }

    public function testToString(): void
    {
        $stockWareSku = new StockWareSku();
        $this->assertSame('', $stockWareSku->__toString());
    }

    protected function createEntity(): object
    {
        return new StockWareSku();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'goodsId' => ['goodsId', '123456789'],
            'skuId' => ['skuId', '987654321'],
            'skuName' => ['skuName', '红色 L码'],
            'quantity' => ['quantity', 50],
            'existWare' => ['existWare', true],
            'status' => ['status', 1],
        ];
    }
}
