<?php

namespace PinduoduoApiBundle\Tests\MessageHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\MessageHandler\SyncOrderListDetailHandler;

/**
 * @internal
 */
#[CoversClass(SyncOrderListDetailHandler::class)]
final class SyncOrderListDetailHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        // No additional setup needed for this test
    }

    public function testHandlerCanBeInstantiated(): void
    {
        $handler = $this->createMock(SyncOrderListDetailHandler::class);

        $this->assertInstanceOf(SyncOrderListDetailHandler::class, $handler);
    }

    public function testHandlerImplementsMessageHandlerInterface(): void
    {
        $reflection = new \ReflectionClass(SyncOrderListDetailHandler::class);

        $this->assertTrue($reflection->hasMethod('__invoke'));
    }
}
