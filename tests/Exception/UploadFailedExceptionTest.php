<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\UploadFailedException;

class UploadFailedExceptionTest extends TestCase
{
    public function testConstruct_withMessage_correctlyInitializedException(): void
    {
        $message = '图片上传失败';
        $exception = new UploadFailedException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
    
    public function testConstruct_withMessageAndCode_correctlyInitializedException(): void
    {
        $message = '图片上传失败';
        $code = 500;
        $exception = new UploadFailedException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
}