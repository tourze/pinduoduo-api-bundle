<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\PddApiException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(PddApiException::class)]
final class PddApiExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return PddApiException::class;
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $errorResponse = [
            'error_msg' => 'Test API error',
            'error_code' => 500,
            'sub_msg' => 'Additional error info',
        ];
        $exception = new PddApiException($errorResponse);

        $this->assertInstanceOf(PddApiException::class, $exception);
        $this->assertEquals('Test API error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals('Additional error info', $exception->getSubMsg());
    }

    public function testExceptionInheritsFromException(): void
    {
        $errorResponse = [
            'error_msg' => 'Test error',
            'error_code' => 400,
        ];
        $exception = new PddApiException($errorResponse);

        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithoutSubMsg(): void
    {
        $errorResponse = [
            'error_msg' => 'Error without sub message',
            'error_code' => 404,
        ];
        $exception = new PddApiException($errorResponse);

        $this->assertEquals('Error without sub message', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
        $this->assertNull($exception->getSubMsg());
    }
}
