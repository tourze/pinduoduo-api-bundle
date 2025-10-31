<?php

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Exception\MallNotFoundException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(MallNotFoundException::class)]
final class MallNotFoundExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return MallNotFoundException::class;
    }
}
