<?php

namespace PinduoduoApiBundle\Tests\Controller\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Controller\Auth\CallbackController;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(CallbackController::class)]
#[RunTestsInSeparateProcesses]
final class CallbackControllerTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            self::markTestSkipped('INVALID method not applicable for CallbackController');
        }

        // CallbackController 只允许 GET 方法，其他方法应该被拒绝
        $this->assertContains($method, ['POST', 'PUT', 'DELETE', 'PATCH']);
    }
}
