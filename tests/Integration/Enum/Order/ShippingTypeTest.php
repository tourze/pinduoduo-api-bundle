<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\ShippingType;

class ShippingTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(ShippingType::class));
    }
}