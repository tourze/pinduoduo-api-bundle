<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\MktBizType;

class MktBizTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MktBizType::class));
    }
}