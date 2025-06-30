<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;

class CategoryRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CategoryRepository::class));
    }
}