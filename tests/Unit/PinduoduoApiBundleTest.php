<?php

namespace PinduoduoApiBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\PinduoduoApiBundle;

class PinduoduoApiBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new PinduoduoApiBundle();
        
        $this->assertInstanceOf(PinduoduoApiBundle::class, $bundle);
    }
}