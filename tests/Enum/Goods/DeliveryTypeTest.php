<?php

namespace PinduoduoApiBundle\Tests\Enum\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryType::class)]
final class DeliveryTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DeliveryType::class));
    }

    public function testToArray(): void
    {
        $enum = DeliveryType::NoDelivery;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
