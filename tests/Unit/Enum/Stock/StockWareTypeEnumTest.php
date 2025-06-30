<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;

class StockWareTypeEnumTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(StockWareTypeEnum::class));
    }
}