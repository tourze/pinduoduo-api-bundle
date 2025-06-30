<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\LogisticsTemplateSyncCommand;

class LogisticsTemplateSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(LogisticsTemplateSyncCommand::class));
    }
}