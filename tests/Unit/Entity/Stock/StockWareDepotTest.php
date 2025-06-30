<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;

class StockWareDepotTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareDepot::class));
    }
}