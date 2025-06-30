<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\CpsProtocolRefreshCommand;

class CpsProtocolRefreshCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CpsProtocolRefreshCommand::class));
    }
}