<?php

namespace PinduoduoApiBundle\Tests\Enum\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DepotTypeEnum::class)]
final class DepotTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotTypeEnum::class));
    }

    public function testToArray(): void
    {
        // 使用第一个枚举值进行测试
        $enum = DepotTypeEnum::cases()[0];
        $result = $enum->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
