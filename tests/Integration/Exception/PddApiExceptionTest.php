<?php

namespace PinduoduoApiBundle\Tests\Integration\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\PddApiException;

class PddApiExceptionTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(PddApiException::class));
    }
}