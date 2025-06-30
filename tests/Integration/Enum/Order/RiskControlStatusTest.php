<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\RiskControlStatus;

class RiskControlStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(RiskControlStatus::class));
    }
}