<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\PinduoduoApiBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(PinduoduoApiBundle::class)]
#[RunTestsInSeparateProcesses]
final class PinduoduoApiBundleTest extends AbstractBundleTestCase
{
}
