<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ConfirmStatus::class)]
final class ConfirmStatusTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(ConfirmStatus::class));
    }

    public function testToArray(): void
    {
        $enum = ConfirmStatus::PENDING;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
