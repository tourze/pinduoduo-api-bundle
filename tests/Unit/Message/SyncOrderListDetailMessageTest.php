<?php

namespace PinduoduoApiBundle\Tests\Unit\Message;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;

class SyncOrderListDetailMessageTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SyncOrderListDetailMessage::class));
    }
}