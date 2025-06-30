<?php

namespace PinduoduoApiBundle\Tests\Integration\Procedure\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\Goods\GetPddMallCatRule;

class GetPddMallCatRuleTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddMallCatRule::class));
    }
}