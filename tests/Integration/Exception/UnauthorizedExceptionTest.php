<?php

namespace PinduoduoApiBundle\Tests\Integration\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\UnauthorizedException;

class UnauthorizedExceptionTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UnauthorizedException::class));
    }
}