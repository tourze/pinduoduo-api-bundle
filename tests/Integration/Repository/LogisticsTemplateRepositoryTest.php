<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;

class LogisticsTemplateRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(LogisticsTemplateRepository::class));
    }
}