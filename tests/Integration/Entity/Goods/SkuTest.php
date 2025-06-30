<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Sku;

class SkuTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Sku::class));
    }
}