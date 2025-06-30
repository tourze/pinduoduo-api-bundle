<?php

namespace PinduoduoApiBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\ExecPddApi;

class ExecPddApiTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(ExecPddApi::class));
    }
}