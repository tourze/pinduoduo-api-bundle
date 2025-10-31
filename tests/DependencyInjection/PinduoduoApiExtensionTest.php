<?php

namespace PinduoduoApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\DependencyInjection\PinduoduoApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(PinduoduoApiExtension::class)]
final class PinduoduoApiExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testExtensionInstantiation(): void
    {
        $extension = new PinduoduoApiExtension();

        $this->assertInstanceOf(PinduoduoApiExtension::class, $extension);
    }

    public function testLoad(): void
    {
        $extension = new PinduoduoApiExtension();
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.environment', 'test');

        $extension->load([], $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('PinduoduoApiBundle\Service\CategoryService'));
    }
}
