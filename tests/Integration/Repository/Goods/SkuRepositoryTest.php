<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;

class SkuRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SkuRepository::class));
    }
}