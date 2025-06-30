<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\UploadImg;

class UploadImgTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadImg::class));
    }
}