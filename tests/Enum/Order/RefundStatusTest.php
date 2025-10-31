<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\RefundStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(RefundStatus::class)]
final class RefundStatusTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(RefundStatus::class));
    }

    public function testToArray(): void
    {
        $enum = RefundStatus::NO_REFUND_OR_CLOSED;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
