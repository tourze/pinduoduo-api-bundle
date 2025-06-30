<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\UnauthorizedException;

class UnauthorizedExceptionTest extends TestCase
{
    public function testConstruct_withMessage_correctlyInitializedException(): void
    {
        $message = '未授权调用：pdd.test.api';
        $exception = new UnauthorizedException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
    
    public function testConstruct_withMessageAndCode_correctlyInitializedException(): void
    {
        $message = '未授权调用：pdd.test.api';
        $code = 401;
        $exception = new UnauthorizedException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
}