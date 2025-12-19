<?php

namespace PinduoduoApiBundle\Tests\Enum\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsType::class)]
final class GoodsTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(GoodsType::class));
    }

    public function testToArray(): void
    {
        $enum = GoodsType::国内普通商品;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
