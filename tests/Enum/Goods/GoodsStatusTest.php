<?php

namespace PinduoduoApiBundle\Tests\Enum\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Goods\GoodsStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsStatus::class)]
final class GoodsStatusTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(GoodsStatus::class));
    }

    public function testToArray(): void
    {
        $enum = GoodsStatus::Up;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
