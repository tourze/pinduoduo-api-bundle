<?php

namespace PinduoduoApiBundle\Tests\Enum\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DepotPriorityTypeEnum::class)]
final class DepotPriorityTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotPriorityTypeEnum::class));
    }

    public function testToArray(): void
    {
        // 使用第一个枚举值进行测试
        $enum = DepotPriorityTypeEnum::cases()[0];
        $result = $enum->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
