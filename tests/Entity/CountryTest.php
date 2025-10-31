<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Country;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Country::class)]
final class CountryTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $country = new Country();
        $this->assertInstanceOf(Country::class, $country);
    }

    public function testGetAndSetName(): void
    {
        $country = new Country();
        $name = 'United States';
        $this->assertNull($country->getName());
        $country->setName($name);
        $this->assertSame($name, $country->getName());
    }

    public function testToString(): void
    {
        $country = new Country();
        $this->assertSame('', $country->__toString());
    }

    protected function createEntity(): object
    {
        return new Country();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'name' => ['name', 'United States'],
        ];
    }
}
