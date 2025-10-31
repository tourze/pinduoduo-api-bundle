<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\UploadFailedException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UploadFailedException::class)]
final class UploadFailedExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return UploadFailedException::class;
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new UploadFailedException('Upload failed');

        $this->assertInstanceOf(UploadFailedException::class, $exception);
        $this->assertEquals('Upload failed', $exception->getMessage());
    }

    public function testExceptionInheritsFromRuntimeException(): void
    {
        $exception = new UploadFailedException('Upload error');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithFilePathAndReason(): void
    {
        $exception = new UploadFailedException('Failed to upload image.jpg: File too large');

        $this->assertEquals('Failed to upload image.jpg: File too large', $exception->getMessage());
        $this->assertInstanceOf(UploadFailedException::class, $exception);
    }
}
