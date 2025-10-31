<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\UploadImg;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(UploadImg::class)]
final class UploadImgTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $uploadImg = new UploadImg();
        $this->assertInstanceOf(UploadImg::class, $uploadImg);
    }

    public function testGetAndSetMall(): void
    {
        $uploadImg = new UploadImg();
        $mall = new Mall();

        $this->assertNull($uploadImg->getMall());

        $uploadImg->setMall($mall);
        $this->assertSame($mall, $uploadImg->getMall());
    }

    public function testGetAndSetFile(): void
    {
        $uploadImg = new UploadImg();
        $file = 'test-image.jpg';

        $this->assertNull($uploadImg->getFile());

        $uploadImg->setFile($file);
        $this->assertSame($file, $uploadImg->getFile());
    }

    public function testGetAndSetUrl(): void
    {
        $uploadImg = new UploadImg();
        $url = 'https://example.com/image.jpg';

        $this->assertNull($uploadImg->getUrl());

        $uploadImg->setUrl($url);
        $this->assertSame($url, $uploadImg->getUrl());
    }

    public function testToString(): void
    {
        $uploadImg = new UploadImg();
        $this->assertSame('', $uploadImg->__toString());
    }

    protected function createEntity(): object
    {
        return new UploadImg();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'file' => ['file', 'test-image.jpg'],
            'url' => ['url', 'https://example.com/image.jpg'],
        ];
    }
}
