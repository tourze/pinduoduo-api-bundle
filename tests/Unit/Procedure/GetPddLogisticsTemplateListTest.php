<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\GetPddLogisticsTemplateList;

class GetPddLogisticsTemplateListTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddLogisticsTemplateList::class));
    }
}