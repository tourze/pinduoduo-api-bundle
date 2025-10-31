<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\Video;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Video::class)]
final class VideoTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $video = new Video();
        $this->assertInstanceOf(Video::class, $video);
    }

    public function testGetAndSetMall(): void
    {
        $video = new Video();
        $mall = new Mall();
        $this->assertNull($video->getMall());
        $video->setMall($mall);
        $this->assertSame($mall, $video->getMall());
    }

    public function testGetAndSetUrl(): void
    {
        $video = new Video();
        $url = 'https://example.com/video.mp4';
        $this->assertNull($video->getUrl());
        $video->setUrl($url);
        $this->assertSame($url, $video->getUrl());
    }

    public function testGetAndSetStatus(): void
    {
        $video = new Video();
        $status = 1;
        $this->assertNull($video->getStatus());
        $video->setStatus($status);
        $this->assertSame($status, $video->getStatus());
    }

    public function testToString(): void
    {
        $video = new Video();
        $this->assertSame('', $video->__toString());
    }

    protected function createEntity(): object
    {
        return new Video();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'url' => ['url', 'https://example.com/video.mp4'],
            'status' => ['status', 1],
        ];
    }
}
