<?php

namespace PinduoduoApiBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\MallNotFoundException;

class MallNotFoundExceptionTest extends TestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new MallNotFoundException();
        
        $this->assertInstanceOf(MallNotFoundException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('找不到授权店铺', $exception->getMessage());
    }
    
    public function testExceptionWithCustomMessage(): void
    {
        $message = '自定义错误消息';
        $exception = new MallNotFoundException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }
}