<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\MerchantType;

class MerchantTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MerchantType::class));
    }
}