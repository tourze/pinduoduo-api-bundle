<?php

namespace PinduoduoApiBundle\Tests\Integration\Command\Goods;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\Goods\CategoryRuleGetCommand;

class CategoryRuleGetCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CategoryRuleGetCommand::class));
    }
}