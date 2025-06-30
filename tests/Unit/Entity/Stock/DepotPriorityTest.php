<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;

class DepotPriorityTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(DepotPriority::class));
    }
}