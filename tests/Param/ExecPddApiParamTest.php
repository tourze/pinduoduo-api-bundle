<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\ExecPddApiParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(ExecPddApiParam::class)]
final class ExecPddApiParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ExecPddApiParam(
            mallId: 'test-mall-id',
            api: 'pdd.goods.get',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParameters(): void
    {
        $param = new ExecPddApiParam(
            mallId: 'mall-123',
            api: 'pdd.goods.list',
        );

        $this->assertSame('mall-123', $param->mallId);
        $this->assertSame('pdd.goods.list', $param->api);
        $this->assertSame([], $param->params);
    }

    public function testConstructorWithAllParameters(): void
    {
        $params = ['goods_id' => 123, 'page' => 1];
        $param = new ExecPddApiParam(
            mallId: 'mall-456',
            api: 'pdd.goods.detail.get',
            params: $params,
        );

        $this->assertSame('mall-456', $param->mallId);
        $this->assertSame('pdd.goods.detail.get', $param->api);
        $this->assertSame($params, $param->params);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(ExecPddApiParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
