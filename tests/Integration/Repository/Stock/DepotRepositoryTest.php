<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;

class DepotRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(DepotRepository::class));
    }
}