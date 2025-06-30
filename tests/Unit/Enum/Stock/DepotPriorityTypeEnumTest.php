<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;

class DepotPriorityTypeEnumTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotPriorityTypeEnum::class));
    }
}