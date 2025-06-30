<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Order\Order;

class OrderTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Order::class));
    }
}