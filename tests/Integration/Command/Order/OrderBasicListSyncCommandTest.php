<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Order\OrderBasicListSyncCommand;

class OrderBasicListSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(OrderBasicListSyncCommand::class));
    }
}