<?php

namespace PinduoduoApiBundle\Tests\Enum\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\Order\AfterSalesStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AfterSalesStatus::class)]
final class AfterSalesStatusTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(AfterSalesStatus::class));
    }

    public function testToArray(): void
    {
        $enum = AfterSalesStatus::无售后;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
