<?php

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Service\AttributeControllerLoader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->load('resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupportsReturnsFalse(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertFalse($service->supports('resource'));
    }
}
