<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\Goods\GetPddGoodsMallSpecValue;

class GetPddGoodsMallSpecValueTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddGoodsMallSpecValue::class));
    }
}