<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\MallCharacter;

class MallCharacterTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MallCharacter::class));
    }
}