<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\AuthCatRepository;

class AuthCatRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AuthCatRepository::class));
    }
}