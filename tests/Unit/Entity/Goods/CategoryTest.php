<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Category;

class CategoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Category::class));
    }
}