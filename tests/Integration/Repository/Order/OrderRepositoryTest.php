<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Order\OrderRepository;

class OrderRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(OrderRepository::class));
    }
}