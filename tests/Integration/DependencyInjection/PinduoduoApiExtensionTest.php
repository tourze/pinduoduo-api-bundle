<?php

namespace PinduoduoApiBundle\Tests\Integration\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\DependencyInjection\PinduoduoApiExtension;

class PinduoduoApiExtensionTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(PinduoduoApiExtension::class));
    }
}