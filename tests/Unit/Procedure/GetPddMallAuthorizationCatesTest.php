<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\GetPddMallAuthorizationCates;

class GetPddMallAuthorizationCatesTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddMallAuthorizationCates::class));
    }
}