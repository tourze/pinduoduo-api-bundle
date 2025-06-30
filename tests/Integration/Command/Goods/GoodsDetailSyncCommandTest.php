<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Goods\GoodsDetailSyncCommand;

class GoodsDetailSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GoodsDetailSyncCommand::class));
    }
}