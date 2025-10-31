<?php

namespace PinduoduoApiBundle\Tests\Entity\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Goods\Sku;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Sku::class)]
final class SkuTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $sku = new Sku();
        $this->assertInstanceOf(Sku::class, $sku);
    }

    public function testToString(): void
    {
        $sku = new Sku();
        $this->assertSame('', $sku->__toString());
    }

    protected function createEntity(): object
    {
        return new Sku();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'onsale' => ['onsale', true],
            'outerSkuId' => ['outerSkuId', 'SKU_EXTERNAL_001'],
            'quantity' => ['quantity', 50],
            'reserveQuantity' => ['reserveQuantity', 10],
            'specName' => ['specName', '红色 L码'],
            'specDetails' => ['specDetails', ['color' => '红色', 'size' => 'L']],
            'outSkuSn' => ['outSkuSn', 'SKU_SN_001'],
            'multiPrice' => ['multiPrice', 9900],
            'thumbUrl' => ['thumbUrl', 'https://example.com/sku-thumb.jpg'],
            'preSaleTime' => ['preSaleTime', new \DateTimeImmutable('+3 days')],
            'length' => ['length', 30],
            'weight' => ['weight', 500],
            'overseaSku' => ['overseaSku', ['oversea' => 'yes']],
            'outSourceSkuId' => ['outSourceSkuId', 'OUT_SOURCE_001'],
            'spec' => ['spec', ['key' => 'color', 'value' => 'red']],
            'price' => ['price', 8800],
            'limitQuantity' => ['limitQuantity', 2],
            'skuProperties' => ['skuProperties', ['property1' => 'value1']],
        ];
    }
}
