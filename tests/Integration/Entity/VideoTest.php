<?php

namespace PinduoduoApiBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Video;

class VideoTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Video::class));
    }
}