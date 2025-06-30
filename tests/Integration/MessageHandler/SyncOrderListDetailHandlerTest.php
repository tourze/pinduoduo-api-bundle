<?php

namespace PinduoduoApiBundle\Tests\Integration\MessageHandler;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\MessageHandler\SyncOrderListDetailHandler;

class SyncOrderListDetailHandlerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SyncOrderListDetailHandler::class));
    }
}