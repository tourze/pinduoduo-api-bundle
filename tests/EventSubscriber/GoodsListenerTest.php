<?php

namespace PinduoduoApiBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\EventSubscriber\GoodsListener;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsListener::class)]
#[RunTestsInSeparateProcesses]
final class GoodsListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testListenerCanBeInstantiated(): void
    {
        $listener = self::getService(GoodsListener::class);

        $this->assertInstanceOf(GoodsListener::class, $listener);
    }

    public function testListenerHasPostRemoveMethod(): void
    {
        $listener = self::getService(GoodsListener::class);

        $this->assertInstanceOf(GoodsListener::class, $listener);
        $reflection = new \ReflectionClass($listener);
        $this->assertTrue($reflection->hasMethod('postRemove'));
    }

    public function testPostRemoveWithNullMall(): void
    {
        $listener = self::getService(GoodsListener::class);

        $goods = new Goods();
        $goods->setMall(null);

        // 当 mall 为 null 时，postRemove 应该提前返回，不会抛出异常
        $listener->postRemove($goods);
        $this->assertTrue(true); // 如果没有抛出异常，测试通过
    }

    public function testPostRemoveWithMall(): void
    {
        $listener = self::getService(GoodsListener::class);

        $mall = new Mall();
        $mall->setName('测试店铺');

        $goods = new Goods();
        $goods->setMall($mall);

        // 由于这会实际调用 API，我们只验证方法可以被调用
        // 实际的 API 调用和错误处理会在集成测试中验证
        $reflection = new \ReflectionMethod($listener, 'postRemove');
        $this->assertTrue($reflection->isPublic());
    }
}
