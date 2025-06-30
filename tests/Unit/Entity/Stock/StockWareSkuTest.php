<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;

class StockWareSkuTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareSku::class));
    }
}