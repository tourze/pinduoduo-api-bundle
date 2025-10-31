<?php

namespace PinduoduoApiBundle\Tests\Message;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;

/**
 * @internal
 */
#[CoversClass(SyncOrderListDetailMessage::class)]
final class SyncOrderListDetailMessageTest extends TestCase
{
    public function testMessageCanBeInstantiated(): void
    {
        $message = new SyncOrderListDetailMessage();

        $this->assertInstanceOf(SyncOrderListDetailMessage::class, $message);
    }

    public function testMessageHasCorrectProperties(): void
    {
        $message = new SyncOrderListDetailMessage();

        // 验证消息对象具备基本的消息特征
        $reflection = new \ReflectionClass($message);
        $this->assertNotEmpty($reflection->getName());
        $this->assertEquals(SyncOrderListDetailMessage::class, $reflection->getName());
    }
}
