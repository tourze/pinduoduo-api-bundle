<?php

namespace PinduoduoApiBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Service\UploadService;

class UploadServiceTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadService::class));
    }
}