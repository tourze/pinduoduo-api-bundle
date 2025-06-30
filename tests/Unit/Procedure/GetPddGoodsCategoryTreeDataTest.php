<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\GetPddGoodsCategoryTreeData;

class GetPddGoodsCategoryTreeDataTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddGoodsCategoryTreeData::class));
    }
}