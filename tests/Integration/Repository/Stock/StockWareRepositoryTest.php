<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;

class StockWareRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareRepository::class));
    }
}