<?php

namespace PinduoduoApiBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AttributeControllerLoader::class));
    }
}