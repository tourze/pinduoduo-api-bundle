<?php

namespace PinduoduoApiBundle\Tests\Integration\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\UploadFailedException;

class UploadFailedExceptionTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadFailedException::class));
    }
}