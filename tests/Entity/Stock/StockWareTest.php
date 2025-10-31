<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(StockWare::class)]
final class StockWareTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $stockWare = new StockWare();
        $this->assertInstanceOf(StockWare::class, $stockWare);
    }

    public function testToString(): void
    {
        $stockWare = new StockWare();
        $this->assertSame('', $stockWare->__toString());
    }

    protected function createEntity(): object
    {
        return new StockWare();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'wareId' => ['wareId', '123456789'],
            'wareSn' => ['wareSn', 'WARE001'],
            'wareName' => ['wareName', '测试货品'],
            'specification' => ['specification', '规格说明'],
            'unit' => ['unit', '个'],
            'brand' => ['brand', '品牌A'],
            'color' => ['color', '红色'],
            'packing' => ['packing', '纸盒包装'],
            'note' => ['note', '货品备注'],
            'grossWeight' => ['grossWeight', 1.50],
            'netWeight' => ['netWeight', 1.20],
            'tareWeight' => ['tareWeight', 0.30],
            'weight' => ['weight', 1.20],
            'length' => ['length', 30.00],
            'width' => ['width', 20.00],
            'height' => ['height', 10.00],
            'volume' => ['volume', 0.006],
            'price' => ['price', 99.99],
            'serviceQuality' => ['serviceQuality', 95],
            'quantity' => ['quantity', 100],
            'wareInfos' => ['wareInfos', ['info1' => 'value1']],
            'createdAt' => ['createdAt', 1640995200],
            'updatedAt' => ['updatedAt', 1640995200],
        ];
    }
}
