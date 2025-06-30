<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;

class StockWareSkuRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareSkuRepository::class));
    }
}