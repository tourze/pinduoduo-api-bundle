<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Goods\SpecSyncCommand;

class SpecSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SpecSyncCommand::class));
    }
}