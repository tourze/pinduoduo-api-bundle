<?php

namespace PinduoduoApiBundle\Tests\Entity\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Measurement::class)]
final class MeasurementTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $measurement = new Measurement();
        $this->assertInstanceOf(Measurement::class, $measurement);
    }

    public function testGetAndSetCode(): void
    {
        $measurement = new Measurement();
        $code = 'KG';
        $measurement->setCode($code);
        $this->assertSame($code, $measurement->getCode());
    }

    public function testGetAndSetDescription(): void
    {
        $measurement = new Measurement();
        $description = 'Kilogram weight unit';
        $this->assertNull($measurement->getDescription());
        $measurement->setDescription($description);
        $this->assertSame($description, $measurement->getDescription());
    }

    public function testToString(): void
    {
        $measurement = new Measurement();
        $this->assertSame('', $measurement->__toString());
    }

    protected function createEntity(): object
    {
        return new Measurement();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'code' => ['code', 'KG'],
            'description' => ['description', 'Kilogram weight unit'],
        ];
    }
}
