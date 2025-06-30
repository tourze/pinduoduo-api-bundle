<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Stock\StockWareSpecRepository;

class StockWareSpecRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(StockWareSpecRepository::class));
    }
}