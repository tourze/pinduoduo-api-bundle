<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Goods;

class GoodsTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Goods::class));
    }
}