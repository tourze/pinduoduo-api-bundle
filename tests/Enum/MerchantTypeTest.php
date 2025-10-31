<?php

namespace PinduoduoApiBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\MerchantType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MerchantType::class)]
final class MerchantTypeTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MerchantType::class));
    }

    public function testToArray(): void
    {
        $enum = MerchantType::个人;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
