<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Goods\GoodsType;

class GoodsTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(GoodsType::class));
    }
}