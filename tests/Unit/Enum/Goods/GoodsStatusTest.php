<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Goods\GoodsStatus;

class GoodsStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(GoodsStatus::class));
    }
}