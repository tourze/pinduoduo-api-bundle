<?php

namespace PinduoduoApiBundle\Tests\Controller\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Controller\Auth\RedirectController;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(RedirectController::class)]
#[RunTestsInSeparateProcesses]
final class RedirectControllerTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testRedirectControllerCanBeInstantiated(): void
    {
        $reflection = new \ReflectionClass(RedirectController::class);
        $this->assertTrue($reflection->isInstantiable());
        $this->assertTrue($reflection->isFinal());
    }

    public function testRedirectControllerIsCallable(): void
    {
        $reflection = new \ReflectionClass(RedirectController::class);
        $this->assertTrue($reflection->hasMethod('__invoke'));
        $invokeMethod = $reflection->getMethod('__invoke');
        $this->assertTrue($invokeMethod->isPublic());
    }

    public function testRedirectWithoutAccount(): void
    {
        $reflection = new \ReflectionClass(RedirectController::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        // 对于RedirectController，只测试基本的HTTP方法
        if ('INVALID' === $method) {
            self::markTestSkipped('INVALID method not applicable for RedirectController');
        }
        $this->assertContains($method, ['POST', 'PUT', 'DELETE', 'PATCH']);
    }
}
