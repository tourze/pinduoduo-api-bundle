<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\Goods\GetPddGoodsMallSpecValueParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsMallSpecValueParam::class)]
final class GetPddGoodsMallSpecValueParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetPddGoodsMallSpecValueParam(
            mallId: 'test-mall-id',
            parentSpecId: 'test-parent-spec-id',
            specName: 'test-spec-name',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-mall-id', $param->mallId);
        $this->assertSame('test-parent-spec-id', $param->parentSpecId);
        $this->assertSame('test-spec-name', $param->specName);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetPddGoodsMallSpecValueParam(
            mallId: 'another-mall-id',
            parentSpecId: 'another-parent-spec-id',
            specName: 'another-spec-name',
        );

        $this->assertSame('another-mall-id', $param->mallId);
        $this->assertSame('another-parent-spec-id', $param->parentSpecId);
        $this->assertSame('another-spec-name', $param->specName);
    }
}
