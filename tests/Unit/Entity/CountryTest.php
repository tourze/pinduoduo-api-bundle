<?php

namespace PinduoduoApiBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Country;

class CountryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Country::class));
    }
}