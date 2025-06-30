<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;

class DepotStatusEnumTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotStatusEnum::class));
    }
}