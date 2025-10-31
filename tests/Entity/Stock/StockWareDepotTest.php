<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareDepot::class)]
final class StockWareDepotTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $stockWareDepot = new StockWareDepot();
        $this->assertInstanceOf(StockWareDepot::class, $stockWareDepot);
    }

    public function testToString(): void
    {
        $stockWareDepot = new StockWareDepot();
        $this->assertSame('', $stockWareDepot->__toString());
    }

    protected function createEntity(): object
    {
        return new StockWareDepot();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'availableQuantity' => ['availableQuantity', 100],
            'occupiedQuantity' => ['occupiedQuantity', 20],
            'lockedQuantity' => ['lockedQuantity', 10],
            'onwayQuantity' => ['onwayQuantity', 5],
            'totalQuantity' => ['totalQuantity', 135],
            'warningThreshold' => ['warningThreshold', 10.00],
            'upperLimit' => ['upperLimit', 200.00],
            'locationCode' => ['locationCode', 'A001'],
            'note' => ['note', '库存备注'],
        ];
    }
}
