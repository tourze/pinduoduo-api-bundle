<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Goods\GoodsSyncListCommand;

class GoodsSyncListCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GoodsSyncListCommand::class));
    }
}