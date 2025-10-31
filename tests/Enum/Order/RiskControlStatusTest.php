<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\RiskControlStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(RiskControlStatus::class)]
final class RiskControlStatusTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(RiskControlStatus::class));
    }

    public function testToArray(): void
    {
        $enum = RiskControlStatus::Normal;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
