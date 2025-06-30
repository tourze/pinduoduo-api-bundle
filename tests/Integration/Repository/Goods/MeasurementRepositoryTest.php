<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Goods\MeasurementRepository;

class MeasurementRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(MeasurementRepository::class));
    }
}