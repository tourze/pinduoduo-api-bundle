<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Order\OrderFullListSyncCommand;

class OrderFullListSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(OrderFullListSyncCommand::class));
    }
}