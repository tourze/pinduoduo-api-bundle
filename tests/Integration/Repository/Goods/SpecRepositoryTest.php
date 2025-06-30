<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Goods\SpecRepository;

class SpecRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SpecRepository::class));
    }
}