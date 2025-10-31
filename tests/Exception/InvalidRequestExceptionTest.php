<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\InvalidRequestException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidRequestException::class)]
final class InvalidRequestExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return InvalidRequestException::class;
    }
}
