<?php

namespace PinduoduoApiBundle\Tests\Integration\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\GetPddMallAuthorizationCates;

class GetPddMallAuthorizationCatesTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetPddMallAuthorizationCates::class));
    }
}