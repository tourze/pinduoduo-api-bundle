<?php

namespace PinduoduoApiBundle\Tests\Integration\Procedure;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\ExecPddApi;

class ExecPddApiTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(ExecPddApi::class));
    }
}