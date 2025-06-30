<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;

class StockWareSpecTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareSpec::class));
    }
}