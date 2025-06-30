<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Goods\CategoryLoopSyncCommand;

class CategoryLoopSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CategoryLoopSyncCommand::class));
    }
}