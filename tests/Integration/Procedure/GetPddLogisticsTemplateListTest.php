<?php

namespace PinduoduoApiBundle\Tests\Integration\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\GetPddLogisticsTemplateList;

class GetPddLogisticsTemplateListTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddLogisticsTemplateList::class));
    }
}