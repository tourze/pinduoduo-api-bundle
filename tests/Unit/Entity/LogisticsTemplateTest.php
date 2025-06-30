<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\LogisticsTemplate;

class LogisticsTemplateTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(LogisticsTemplate::class));
    }
}