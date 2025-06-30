<?php

namespace PinduoduoApiBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\EventSubscriber\GoodsListener;

class GoodsListenerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GoodsListener::class));
    }
}