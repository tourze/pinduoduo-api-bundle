<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Order\OrderIncrementListSyncCommand;

class OrderIncrementListSyncCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(OrderIncrementListSyncCommand::class));
    }
}