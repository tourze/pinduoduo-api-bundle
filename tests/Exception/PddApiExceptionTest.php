<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\PddApiException;

class PddApiExceptionTest extends TestCase
{
    public function testConstruct_withCompleteErrorResponse_correctlyInitializedException(): void
    {
        $errorResponse = [
            'error_msg' => '测试错误消息',
            'error_code' => 1001,
            'sub_msg' => '子消息详情'
        ];
        
        $exception = new PddApiException($errorResponse);
        
        $this->assertEquals('测试错误消息', $exception->getMessage());
        $this->assertEquals(1001, $exception->getCode());
        $this->assertEquals('子消息详情', $exception->getSubMsg());
    }
    
    public function testConstruct_withoutSubMsg_subMsgIsNull(): void
    {
        $errorResponse = [
            'error_msg' => '测试错误消息',
            'error_code' => 1002
        ];
        
        $exception = new PddApiException($errorResponse);
        
        $this->assertEquals('测试错误消息', $exception->getMessage());
        $this->assertEquals(1002, $exception->getCode());
        $this->assertNull($exception->getSubMsg());
    }
    
    public function testSetAndGetSubMsg_validSubMsg_subMsgIsSet(): void
    {
        $errorResponse = [
            'error_msg' => '测试错误消息',
            'error_code' => 1003
        ];
        
        $exception = new PddApiException($errorResponse);
        $exception->setSubMsg('新的子消息');
        
        $this->assertEquals('新的子消息', $exception->getSubMsg());
    }
} 