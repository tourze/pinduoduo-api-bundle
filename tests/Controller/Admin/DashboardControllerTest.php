<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Controller\Admin\DashboardController;
use Symfony\Component\HttpFoundation\Response;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(DashboardController::class)]
#[RunTestsInSeparateProcesses]
final class DashboardControllerTest extends AbstractWebTestCase
{
    public function testClassExists(): void
    {
        $controller = new DashboardController();
        $this->assertInstanceOf(DashboardController::class, $controller);
    }

    public function testIndexMethodExists(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        $this->assertTrue($reflection->hasMethod('index'));
    }

    public function testConfigureDashboardMethodExists(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        $this->assertTrue($reflection->hasMethod('configureDashboard'));
    }

    public function testConfigureMenuItemsMethodExists(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        $this->assertTrue($reflection->hasMethod('configureMenuItems'));
    }

    public function testIndexMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'index');
        $returnType = $reflection->getReturnType();

        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getName());
    }

    public function testConfigureDashboardMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'configureDashboard');
        $returnType = $reflection->getReturnType();

        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard', $returnType->getName());
    }

    public function testConfigureMenuItemsMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'configureMenuItems');
        $returnType = $reflection->getReturnType();

        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('iterable', $returnType->getName());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        // DashboardController没有__invoke方法，跳过INVALID方法测试
        if ('INVALID' === $method) {
            self::markTestSkipped('INVALID method not applicable for DashboardController');
        }

        $client = self::createClient();
        $client->request($method, '/pinduoduo-api/admin');

        // 期望方法不允许或重定向（405 或 302）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [405, 302], true),
            "Expected status 405 or 302, got {$statusCode} for method {$method}"
        );
    }
}
