<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\MessageHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;
use PinduoduoApiBundle\MessageHandler\SyncOrderListDetailHandler;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SyncOrderListDetailHandler::class)]
#[RunTestsInSeparateProcesses]
final class SyncOrderListDetailHandlerTest extends AbstractIntegrationTestCase
{
    private SyncOrderListDetailHandler $handler;
    private OrderRepository $orderRepository;

    protected function onSetUp(): void
    {
        $this->handler = self::getService(SyncOrderListDetailHandler::class);
        $this->orderRepository = self::getService(OrderRepository::class);
    }

    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SyncOrderListDetailHandler::class, $this->handler);
    }

    public function testHandlerImplementsMessageHandlerInterface(): void
    {
        $reflection = new \ReflectionClass(SyncOrderListDetailHandler::class);

        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testInvokeCreatesNewOrder(): void
    {
        $orderSn = 'TEST_ORDER_' . uniqid();

        $message = new SyncOrderListDetailMessage();
        $message->setMallId('test_mall_id');
        $message->setOrderInfo([
            'order_sn' => $orderSn,
        ]);

        ($this->handler)($message);

        $order = $this->orderRepository->findOneBy(['orderSn' => $orderSn]);
        $this->assertNotNull($order);
        $this->assertSame($orderSn, $order->getOrderSn());
    }

    public function testInvokeUpdatesExistingOrder(): void
    {
        $orderSn = 'EXISTING_ORDER_' . uniqid();

        // 首先创建一个订单
        $existingOrder = new Order();
        $existingOrder->setOrderSn($orderSn);
        self::getEntityManager()->persist($existingOrder);
        self::getEntityManager()->flush();

        $originalId = $existingOrder->getId();

        // 清除实体管理器以模拟新请求
        self::getEntityManager()->clear();

        // 处理消息
        $message = new SyncOrderListDetailMessage();
        $message->setMallId('test_mall_id');
        $message->setOrderInfo([
            'order_sn' => $orderSn,
        ]);

        ($this->handler)($message);

        // 验证订单没有被重复创建
        $orders = $this->orderRepository->findBy(['orderSn' => $orderSn]);
        $this->assertCount(1, $orders);
        $this->assertSame($originalId, $orders[0]->getId());
    }

    public function testInvokeWithEmptyOrderSn(): void
    {
        $initialCount = count($this->orderRepository->findAll());

        $message = new SyncOrderListDetailMessage();
        $message->setMallId('test_mall_id');
        $message->setOrderInfo([
            'order_sn' => '',
        ]);

        ($this->handler)($message);

        // 应该不会创建新订单
        $finalCount = count($this->orderRepository->findAll());
        $this->assertSame($initialCount, $finalCount);
    }

    public function testInvokeWithMissingOrderSn(): void
    {
        $initialCount = count($this->orderRepository->findAll());

        $message = new SyncOrderListDetailMessage();
        $message->setMallId('test_mall_id');
        $message->setOrderInfo([]);

        ($this->handler)($message);

        // 应该不会创建新订单
        $finalCount = count($this->orderRepository->findAll());
        $this->assertSame($initialCount, $finalCount);
    }
}
