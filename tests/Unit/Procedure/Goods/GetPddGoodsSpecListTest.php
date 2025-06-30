<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\Goods\GetPddGoodsSpecList;

class GetPddGoodsSpecListTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddGoodsSpecList::class));
    }
}