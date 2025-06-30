<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\AuthLog;

class AuthLogTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AuthLog::class));
    }
}