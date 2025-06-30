<?php

namespace PinduoduoApiBundle\Tests\Unit\Enum\Stock;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;

class DepotTypeEnumTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DepotTypeEnum::class));
    }
}