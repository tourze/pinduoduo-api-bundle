<?php

namespace PinduoduoApiBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Service\CategoryService;

class CategoryServiceTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CategoryService::class));
    }
}