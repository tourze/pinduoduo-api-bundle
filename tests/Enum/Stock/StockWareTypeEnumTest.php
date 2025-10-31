<?php

namespace PinduoduoApiBundle\Tests\Enum\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(StockWareTypeEnum::class)]
final class StockWareTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(StockWareTypeEnum::class));
    }

    public function testToArray(): void
    {
        // 使用第一个枚举值进行测试
        $enum = StockWareTypeEnum::cases()[0];
        $result = $enum->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
