<?php

namespace PinduoduoApiBundle\Tests\Repository\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Enum\Order\AfterSalesStatus;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(OrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class OrderRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(OrderRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allOrders = $repository->findAll();
        foreach ($allOrders as $order) {
            $repository->remove($order);
        }

        // 创建关联的 Category 并持久化
        $category = new Category();
        $category->setName('Test Category for Order');
        $category->setLevel(1);
        self::getEntityManager()->persist($category);

        // 添加一个测试数据以满足 DataFixtures 检查
        $order = new Order();
        $order->setOrderSn('TEST_ORDER_001');
        $order->setCategory($category);
        $order->setAfterSalesStatus(AfterSalesStatus::无售后);
        $order->setConfirmStatus(ConfirmStatus::PENDING);
        $order->setOrderStatus(OrderStatus::Received);

        $repository->save($order);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(OrderRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindOrder(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order = new Order();
        $order->setOrderSn('ORDER123456789');
        $order->setBuyerMemo('测试订单备注');
        $order->setDiscountAmount(10.50);
        $order->setOrderStatus(OrderStatus::Sent);

        $repository->save($order);

        $foundOrder = $repository->find($order->getId());
        $this->assertNotNull($foundOrder);
        $this->assertSame('ORDER123456789', $foundOrder->getOrderSn());
        $this->assertSame('测试订单备注', $foundOrder->getBuyerMemo());
        $this->assertSame(10.50, $foundOrder->getDiscountAmount());
        $this->assertSame(OrderStatus::Sent, $foundOrder->getOrderStatus());
    }

    public function testFindOneByOrderSn(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order = new Order();
        $order->setOrderSn('UNIQUE_ORDER_SN');
        $order->setBuyerMemo('唯一订单');

        $repository->save($order);

        $foundOrder = $repository->findOneBy(['orderSn' => 'UNIQUE_ORDER_SN']);
        $this->assertNotNull($foundOrder);
        $this->assertSame('UNIQUE_ORDER_SN', $foundOrder->getOrderSn());
        $this->assertSame('唯一订单', $foundOrder->getBuyerMemo());
    }

    public function testFindByOrderStatus(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order1 = new Order();
        $order1->setOrderSn('ORDER_SHIPPED_001');
        $order1->setOrderStatus(OrderStatus::Sent);

        $order2 = new Order();
        $order2->setOrderSn('ORDER_SHIPPED_002');
        $order2->setOrderStatus(OrderStatus::Sent);

        $order3 = new Order();
        $order3->setOrderSn('ORDER_PENDING_001');
        $order3->setOrderStatus(OrderStatus::Pending);

        $repository->save($order1);
        $repository->save($order2);
        $repository->save($order3);

        $sentOrders = $repository->findBy(['orderStatus' => OrderStatus::Sent]);
        $this->assertCount(2, $sentOrders);

        $pendingOrders = $repository->findBy(['orderStatus' => OrderStatus::Pending]);
        $this->assertCount(1, $pendingOrders);
    }

    public function testFindByAfterSalesStatus(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order1 = new Order();
        $order1->setOrderSn('ORDER_NO_REFUND');
        $order1->setAfterSalesStatus(AfterSalesStatus::无售后);

        $order2 = new Order();
        $order2->setOrderSn('ORDER_REFUNDED');
        $order2->setAfterSalesStatus(AfterSalesStatus::退款成功);

        $repository->save($order1);
        $repository->save($order2);

        $noRefundOrders = $repository->findBy(['afterSalesStatus' => AfterSalesStatus::无售后]);
        $this->assertNotEmpty($noRefundOrders);

        $refundedOrders = $repository->findBy(['afterSalesStatus' => AfterSalesStatus::退款成功]);
        $this->assertNotEmpty($refundedOrders);
    }

    public function testFindByConfirmStatus(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order1 = new Order();
        $order1->setOrderSn('ORDER_CONFIRMED');
        $order1->setConfirmStatus(ConfirmStatus::DEAL);

        $order2 = new Order();
        $order2->setOrderSn('ORDER_UNCONFIRMED');
        $order2->setConfirmStatus(ConfirmStatus::PENDING);

        $repository->save($order1);
        $repository->save($order2);

        $confirmedOrders = $repository->findBy(['confirmStatus' => ConfirmStatus::DEAL]);
        $this->assertNotEmpty($confirmedOrders);

        $unconfirmedOrders = $repository->findBy(['confirmStatus' => ConfirmStatus::PENDING]);
        $this->assertNotEmpty($unconfirmedOrders);
    }

    public function testFindByCategory(): void
    {
        $repository = self::getService(OrderRepository::class);

        $category1 = new Category();
        $category1->setName('Electronics');
        $category1->setLevel(1);
        self::getEntityManager()->persist($category1);

        $category2 = new Category();
        $category2->setName('Clothing');
        $category2->setLevel(1);
        self::getEntityManager()->persist($category2);

        $order1 = new Order();
        $order1->setOrderSn('ORDER_ELECTRONICS');
        $order1->setCategory($category1);

        $order2 = new Order();
        $order2->setOrderSn('ORDER_CLOTHING');
        $order2->setCategory($category2);

        $repository->save($order1);
        $repository->save($order2);

        $electronicsOrders = $repository->findBy(['category' => $category1]);
        $this->assertCount(1, $electronicsOrders);

        $clothingOrders = $repository->findBy(['category' => $category2]);
        $this->assertCount(1, $clothingOrders);
    }

    public function testFindAllReturnsAllOrders(): void
    {
        $repository = self::getService(OrderRepository::class);

        // 清空现有数据
        $allOrders = $repository->findAll();
        foreach ($allOrders as $order) {
            $repository->remove($order);
        }

        $order1 = new Order();
        $order1->setOrderSn('ORDER_001');

        $order2 = new Order();
        $order2->setOrderSn('ORDER_002');

        $repository->save($order1);
        $repository->save($order2);

        $orders = $repository->findAll();
        $this->assertCount(2, $orders);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(OrderRepository::class);

        // 清理现有数据
        $allOrders = $repository->findAll();
        foreach ($allOrders as $order) {
            $repository->remove($order);
        }

        for ($i = 1; $i <= 5; ++$i) {
            $order = new Order();
            $order->setOrderSn("ORDER_{$i}");
            $order->setBuyerMemo("订单 {$i}");
            $repository->save($order);
        }

        $orders = $repository->findBy([], ['orderSn' => 'ASC'], 2, 1);
        $this->assertCount(2, $orders);
        $this->assertSame('ORDER_2', $orders[0]->getOrderSn());
        $this->assertSame('ORDER_3', $orders[1]->getOrderSn());
    }

    public function testFindByWithNullFields(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order = new Order();
        $order->setOrderSn('ORDER_NULL_FIELDS');
        $order->setAfterSalesStatus(null);
        $order->setBuyerMemo(null);
        $order->setBondedWarehouse(null);
        $order->setConfirmStatus(null);
        $order->setOrderStatus(null);
        $order->setRefundStatus(null);
        $order->setPayType(null);
        $order->setCategory(null);

        $repository->save($order);

        $ordersWithNullAfterSales = $repository->findBy(['afterSalesStatus' => null]);
        $this->assertNotEmpty($ordersWithNullAfterSales);

        $ordersWithNullBuyerMemo = $repository->findBy(['buyerMemo' => null]);
        $this->assertNotEmpty($ordersWithNullBuyerMemo);

        $ordersWithNullBondedWarehouse = $repository->findBy(['bondedWarehouse' => null]);
        $this->assertNotEmpty($ordersWithNullBondedWarehouse);

        $ordersWithNullConfirmStatus = $repository->findBy(['confirmStatus' => null]);
        $this->assertNotEmpty($ordersWithNullConfirmStatus);

        $ordersWithNullOrderStatus = $repository->findBy(['orderStatus' => null]);
        $this->assertNotEmpty($ordersWithNullOrderStatus);

        $ordersWithNullRefundStatus = $repository->findBy(['refundStatus' => null]);
        $this->assertNotEmpty($ordersWithNullRefundStatus);

        $ordersWithNullPayType = $repository->findBy(['payType' => null]);
        $this->assertNotEmpty($ordersWithNullPayType);

        $ordersWithNullCategory = $repository->findBy(['category' => null]);
        $this->assertNotEmpty($ordersWithNullCategory);
    }

    public function testFindByBooleanFields(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order = new Order();
        $order->setOrderSn('ORDER_BOOLEAN_FIELDS');
        $order->setSupportNationwideWarranty(true);
        $order->setFreeSf(false);
        $order->setReturnFreightPayer(null);
        $order->setDeliveryOneDay(true);
        $order->setStockOut(false);

        $repository->save($order);

        $ordersWithWarranty = $repository->findBy(['supportNationwideWarranty' => true]);
        $this->assertNotEmpty($ordersWithWarranty);

        $ordersWithoutFreeSf = $repository->findBy(['freeSf' => false]);
        $this->assertNotEmpty($ordersWithoutFreeSf);

        $ordersWithNullReturnFreight = $repository->findBy(['returnFreightPayer' => null]);
        $this->assertNotEmpty($ordersWithNullReturnFreight);

        $ordersWithOneDayDelivery = $repository->findBy(['deliveryOneDay' => true]);
        $this->assertNotEmpty($ordersWithOneDayDelivery);

        $ordersNotStockOut = $repository->findBy(['stockOut' => false]);
        $this->assertNotEmpty($ordersNotStockOut);
    }

    public function testRemoveOrder(): void
    {
        $repository = self::getService(OrderRepository::class);

        $order = new Order();
        $order->setOrderSn('ORDER_TO_BE_REMOVED');

        $repository->save($order);
        $id = $order->getId();

        $repository->remove($order);

        $foundOrder = $repository->find($id);
        $this->assertNull($foundOrder);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(OrderRepository::class);

        $this->clearAllOrders($repository);

        $order1 = new Order();
        $order1->setOrderSn('ORDER_C');
        $order1->setDiscountAmount(30.0);
        $this->persistAndFlush($order1);

        $order2 = new Order();
        $order2->setOrderSn('ORDER_A');
        $order2->setDiscountAmount(10.0);
        $this->persistAndFlush($order2);

        $order3 = new Order();
        $order3->setOrderSn('ORDER_B');
        $order3->setDiscountAmount(20.0);
        $this->persistAndFlush($order3);

        $firstOrderAsc = $repository->findOneBy([], ['orderSn' => 'ASC']);
        $this->assertNotNull($firstOrderAsc);
        $this->assertSame('ORDER_A', $firstOrderAsc->getOrderSn());

        $firstOrderDesc = $repository->findOneBy([], ['orderSn' => 'DESC']);
        $this->assertNotNull($firstOrderDesc);
        $this->assertSame('ORDER_C', $firstOrderDesc->getOrderSn());

        $lowestDiscountOrder = $repository->findOneBy([], ['discountAmount' => 'ASC']);
        $this->assertNotNull($lowestDiscountOrder);
        $this->assertSame(10.0, $lowestDiscountOrder->getDiscountAmount());

        $newestOrder = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestOrder);
        $this->assertSame($order3->getId(), $newestOrder->getId());
    }

    private function clearAllOrders(OrderRepository $repository): void
    {
        $allOrders = $repository->findAll();
        foreach ($allOrders as $order) {
            self::getEntityManager()->remove($order);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Order
    {
        // 创建 Category 对象，但不持久化
        $category = new Category();
        $category->setName('Test Category for Order ' . uniqid());
        $category->setLevel(1);

        $entity = new Order();
        $entity->setOrderSn('TEST_ORDER_' . uniqid());
        $entity->setCategory($category);
        $entity->setBuyerMemo('Test Order Memo ' . uniqid());
        $entity->setDiscountAmount(9.99);
        $entity->setOrderStatus(OrderStatus::Pending);
        $entity->setConfirmStatus(ConfirmStatus::PENDING);
        $entity->setAfterSalesStatus(AfterSalesStatus::无售后);

        return $entity;
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityShouldPersistedSuccessWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $category = $entity->getCategory();
        if (null !== $category) {
            $entityManager->persist($category);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityAndDetachShouldNotInIdentityMapWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $category = $entity->getCategory();
        if (null !== $category) {
            $entityManager->persist($category);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));

        $entityManager->detach($entity);
        $this->assertFalse($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    protected function getRepository(): OrderRepository
    {
        return self::getService(OrderRepository::class);
    }
}
