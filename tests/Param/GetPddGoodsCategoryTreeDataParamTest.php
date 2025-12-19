<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\GetPddGoodsCategoryTreeDataParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsCategoryTreeDataParam::class)]
final class GetPddGoodsCategoryTreeDataParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetPddGoodsCategoryTreeDataParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructed(): void
    {
        $param = new GetPddGoodsCategoryTreeDataParam();

        $this->assertInstanceOf(GetPddGoodsCategoryTreeDataParam::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetPddGoodsCategoryTreeDataParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
