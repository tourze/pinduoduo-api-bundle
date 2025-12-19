<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param\Img;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\Img\UploadPddMallImageParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(UploadPddMallImageParam::class)]
final class UploadPddMallImageParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new UploadPddMallImageParam(
            mallId: 'test-mall-id',
            imgUrl: 'https://example.com/test-image.jpg',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-mall-id', $param->mallId);
        $this->assertSame('https://example.com/test-image.jpg', $param->imgUrl);
    }

    public function testParamIsReadonly(): void
    {
        $param = new UploadPddMallImageParam(
            mallId: 'another-mall-id',
            imgUrl: 'https://example.com/another-image.png',
        );

        $this->assertSame('another-mall-id', $param->mallId);
        $this->assertSame('https://example.com/another-image.png', $param->imgUrl);
    }
}
