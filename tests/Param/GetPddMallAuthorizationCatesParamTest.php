<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\GetPddMallAuthorizationCatesParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetPddMallAuthorizationCatesParam::class)]
final class GetPddMallAuthorizationCatesParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetPddMallAuthorizationCatesParam(mallId: 'test-mall-id');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParameterOnly(): void
    {
        $param = new GetPddMallAuthorizationCatesParam(mallId: 'mall-123');

        $this->assertSame('mall-123', $param->mallId);
        $this->assertSame(0, $param->parentCatId);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new GetPddMallAuthorizationCatesParam(
            mallId: 'mall-456',
            parentCatId: 100,
        );

        $this->assertSame('mall-456', $param->mallId);
        $this->assertSame(100, $param->parentCatId);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetPddMallAuthorizationCatesParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
