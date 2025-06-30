<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\Stock\DepotPriorityRepository;

class DepotPriorityRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(DepotPriorityRepository::class));
    }
}