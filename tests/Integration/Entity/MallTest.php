<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Mall;

class MallTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Mall::class));
    }
}