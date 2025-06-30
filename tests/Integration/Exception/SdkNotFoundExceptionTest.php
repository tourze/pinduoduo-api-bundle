<?php

namespace PinduoduoApiBundle\Tests\Integration\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\SdkNotFoundException;

class SdkNotFoundExceptionTest extends TestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new SdkNotFoundException();
        
        $this->assertInstanceOf(SdkNotFoundException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('找不到SDK授权', $exception->getMessage());
    }
    
    public function testExceptionWithCustomMessage(): void
    {
        $message = '自定义错误消息';
        $exception = new SdkNotFoundException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }
}