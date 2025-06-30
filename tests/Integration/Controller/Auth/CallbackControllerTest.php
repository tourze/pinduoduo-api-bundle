<?php

namespace PinduoduoApiBundle\Tests\Integration\Controller\Auth;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Controller\Auth\CallbackController;

class CallbackControllerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CallbackController::class));
    }
}