<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\UploadImgRepository;

class UploadImgRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadImgRepository::class));
    }
}