<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Measurement;

class MeasurementTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Measurement::class));
    }
}