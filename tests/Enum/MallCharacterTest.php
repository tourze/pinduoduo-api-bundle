<?php

namespace PinduoduoApiBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\MallCharacter;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MallCharacter::class)]
final class MallCharacterTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MallCharacter::class));
    }

    public function testToArray(): void
    {
        $enum = MallCharacter::MANUFACTURER;
        $result = $enum->toArray();

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
