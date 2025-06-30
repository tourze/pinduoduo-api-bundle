<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Spec;

class SpecTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Spec::class));
    }
}