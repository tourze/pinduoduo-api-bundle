<?php

namespace PinduoduoApiBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\DependencyInjection\PinduoduoApiExtension;

class PinduoduoApiExtensionTest extends TestCase
{
    public function testExtensionInstantiation(): void
    {
        $extension = new PinduoduoApiExtension();
        
        $this->assertInstanceOf(PinduoduoApiExtension::class, $extension);
    }
}