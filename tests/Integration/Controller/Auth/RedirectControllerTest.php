<?php

namespace PinduoduoApiBundle\Tests\Integration\Controller\Auth;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Controller\Auth\RedirectController;

class RedirectControllerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(RedirectController::class));
    }
}