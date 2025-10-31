<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\SdkNotFoundException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(SdkNotFoundException::class)]
final class SdkNotFoundExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return SdkNotFoundException::class;
    }
}
