<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\PayType;

class PayTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(PayType::class));
    }
}