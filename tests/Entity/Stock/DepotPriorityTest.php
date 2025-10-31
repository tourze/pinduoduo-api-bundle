<?php

namespace PinduoduoApiBundle\Tests\Entity\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DepotPriority::class)]
final class DepotPriorityTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $depotPriority = new DepotPriority();
        $this->assertInstanceOf(DepotPriority::class, $depotPriority);
    }

    public function testToString(): void
    {
        $depotPriority = new DepotPriority();
        $this->assertSame('', $depotPriority->__toString());
    }

    protected function createEntity(): object
    {
        return new DepotPriority();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'depotCode' => ['depotCode', 'DEPOT001'],
            'depotId' => ['depotId', '123456789'],
            'depotName' => ['depotName', '主仓库'],
            'provinceId' => ['provinceId', 11],
            'cityId' => ['cityId', 1101],
            'districtId' => ['districtId', 110101],
            'priority' => ['priority', 1],
        ];
    }
}
