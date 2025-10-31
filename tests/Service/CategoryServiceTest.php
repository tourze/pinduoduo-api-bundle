<?php

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Service\CategoryService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(CategoryService::class)]
#[RunTestsInSeparateProcesses]
final class CategoryServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for this test
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(CategoryService::class);
        $this->assertInstanceOf(CategoryService::class, $service);
    }

    public function testSyncSpecList(): void
    {
        // 基本的测试，确保方法可以被调用
        self::markTestSkipped('CategoryService::syncSpecList() 需要有效的数据库和 API 数据进行完整测试');
    }
}
