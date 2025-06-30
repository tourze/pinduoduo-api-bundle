<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\StockOutHandleStatus;

class StockOutHandleStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(StockOutHandleStatus::class));
    }
}