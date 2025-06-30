<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Stock\Depot;

class DepotTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Depot::class));
    }
}