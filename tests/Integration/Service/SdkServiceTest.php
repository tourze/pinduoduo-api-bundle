<?php

namespace PinduoduoApiBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Service\SdkService;

class SdkServiceTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SdkService::class));
    }
}