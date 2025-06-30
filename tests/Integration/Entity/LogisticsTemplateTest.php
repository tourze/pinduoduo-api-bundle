<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\LogisticsTemplate;

class LogisticsTemplateTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(LogisticsTemplate::class));
    }
}