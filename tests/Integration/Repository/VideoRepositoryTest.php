<?php

namespace PinduoduoApiBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Repository\VideoRepository;

class VideoRepositoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(VideoRepository::class));
    }
}