<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\OrderStatus;

class OrderStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(OrderStatus::class));
    }
}