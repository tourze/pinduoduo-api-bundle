<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\CountryRepository;

class CountryRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CountryRepository::class));
    }
}