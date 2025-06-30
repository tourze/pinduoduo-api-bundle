<?php

namespace PinduoduoApiBundle\Tests\Integration\Enum\Order;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\Order\GroupStatus;

class GroupStatusTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(GroupStatus::class));
    }
}