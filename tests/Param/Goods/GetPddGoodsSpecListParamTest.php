<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\Goods\GetPddGoodsSpecListParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsSpecListParam::class)]
final class GetPddGoodsSpecListParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetPddGoodsSpecListParam(
            mallId: 'test-mall-id',
            categoryId: 'test-category-id',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-mall-id', $param->mallId);
        $this->assertSame('test-category-id', $param->categoryId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetPddGoodsSpecListParam(
            mallId: 'another-mall-id',
            categoryId: 'another-category-id',
        );

        $this->assertSame('another-mall-id', $param->mallId);
        $this->assertSame('another-category-id', $param->categoryId);
    }
}
