<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\AccessTokenRefreshCommand;

class AccessTokenRefreshCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AccessTokenRefreshCommand::class));
    }
}