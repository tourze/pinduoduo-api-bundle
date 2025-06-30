<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;

class GoodsRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GoodsRepository::class));
    }
}