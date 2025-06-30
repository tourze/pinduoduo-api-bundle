<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\CostType;

class CostTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(CostType::class));
    }
}