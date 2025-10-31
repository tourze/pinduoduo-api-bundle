<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\TradeType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(TradeType::class)]
final class TradeTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(TradeType::class));
    }

    public function testToArray(): void
    {
        $enum = TradeType::普通订单;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
