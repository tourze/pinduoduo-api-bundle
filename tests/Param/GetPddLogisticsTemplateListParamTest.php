<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Param\GetPddLogisticsTemplateListParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetPddLogisticsTemplateListParam::class)]
final class GetPddLogisticsTemplateListParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetPddLogisticsTemplateListParam(mallId: 'test-mall-id');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithMallId(): void
    {
        $param = new GetPddLogisticsTemplateListParam(mallId: 'mall-123');

        $this->assertSame('mall-123', $param->mallId);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetPddLogisticsTemplateListParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
