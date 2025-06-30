<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Sku;

class SkuTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Sku::class));
    }
}