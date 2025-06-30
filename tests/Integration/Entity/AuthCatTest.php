<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\AuthCat;

class AuthCatTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AuthCat::class));
    }
}