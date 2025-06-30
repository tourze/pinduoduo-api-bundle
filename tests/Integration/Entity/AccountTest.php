<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Account;

class AccountTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Account::class));
    }
}