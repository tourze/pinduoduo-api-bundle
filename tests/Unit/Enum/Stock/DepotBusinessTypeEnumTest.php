<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;

class DepotBusinessTypeEnumTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotBusinessTypeEnum::class));
    }
}