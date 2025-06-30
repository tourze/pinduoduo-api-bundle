<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\MallInfoSyncCommand;

class MallInfoSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(MallInfoSyncCommand::class));
    }
}