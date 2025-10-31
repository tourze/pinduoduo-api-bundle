<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\ShippingType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ShippingType::class)]
final class ShippingTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(ShippingType::class));
    }

    public function testToArray(): void
    {
        $enum = ShippingType::Appointment;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
