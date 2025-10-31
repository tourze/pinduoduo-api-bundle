<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\UnauthorizedException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UnauthorizedException::class)]
final class UnauthorizedExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return UnauthorizedException::class;
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new UnauthorizedException('Unauthorized access');

        $this->assertInstanceOf(UnauthorizedException::class, $exception);
        $this->assertEquals('Unauthorized access', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \RuntimeException('Previous error');
        $exception = new UnauthorizedException('Unauthorized', 401, $previous);

        $this->assertEquals('Unauthorized', $exception->getMessage());
        $this->assertEquals(401, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
