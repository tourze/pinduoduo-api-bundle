<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\AfterSalesStatus;

class AfterSalesStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(AfterSalesStatus::class));
    }
}