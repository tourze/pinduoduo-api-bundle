<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;

class DeliveryTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DeliveryType::class));
    }
}