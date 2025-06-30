<?php

namespace PinduoduoApiBundle\Tests\Integration\Procedure\Img;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Procedure\Img\UploadPddMallImage;

class UploadPddMallImageTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(UploadPddMallImage::class));
    }
}