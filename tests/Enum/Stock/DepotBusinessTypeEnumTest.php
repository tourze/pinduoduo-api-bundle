<?php

namespace PinduoduoApiBundle\Tests\Enum\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DepotBusinessTypeEnum::class)]
final class DepotBusinessTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotBusinessTypeEnum::class));
    }

    public function testToArray(): void
    {
        $result = DepotBusinessTypeEnum::NORMAL->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
