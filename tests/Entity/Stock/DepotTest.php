<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\Depot;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Depot::class)]
final class DepotTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $depot = new Depot();
        $this->assertInstanceOf(Depot::class, $depot);
    }

    public function testToString(): void
    {
        $depot = new Depot();
        $this->assertSame('', $depot->__toString());
    }

    protected function createEntity(): object
    {
        return new Depot();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'depotId' => ['depotId', '987654321'],
            'depotCode' => ['depotCode', 'WAREHOUSE_001'],
            'depotName' => ['depotName', '主仓库'],
            'depotAlias' => ['depotAlias', '仓库A'],
            'contact' => ['contact', '张三'],
            'phone' => ['phone', '13800138000'],
            'address' => ['address', '北京市朝阳区XX路XX号'],
            'province' => ['province', 11],
            'city' => ['city', 1101],
            'district' => ['district', 110101],
            'zipCode' => ['zipCode', '100000'],
            'region' => ['region', ['province' => 11, 'city' => 1101]],
            'otherRegion' => ['otherRegion', ['region' => 'other']],
            'area' => ['area', 1000.50],
            'capacity' => ['capacity', 5000.00],
            'usedCapacity' => ['usedCapacity', 2500.00],
            'locationCount' => ['locationCount', 100],
            'usedLocationCount' => ['usedLocationCount', 50],
        ];
    }
}
