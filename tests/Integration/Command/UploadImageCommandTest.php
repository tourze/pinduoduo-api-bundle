<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\UploadImageCommand;

class UploadImageCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadImageCommand::class));
    }
}