<?php

namespace PinduoduoApiBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\CostType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(CostType::class)]
final class CostTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(CostType::class));
    }

    public function testToArray(): void
    {
        $enum = CostType::ByAmount;
        $result = $enum->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
