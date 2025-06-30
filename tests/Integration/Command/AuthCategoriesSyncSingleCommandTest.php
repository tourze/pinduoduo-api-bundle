<?php

namespace PinduoduoApiBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Command\AuthCategoriesSyncSingleCommand;

class AuthCategoriesSyncSingleCommandTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(AuthCategoriesSyncSingleCommand::class));
    }
}