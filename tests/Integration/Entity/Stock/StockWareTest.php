<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\StockWare;

class StockWareTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWare::class));
    }
}