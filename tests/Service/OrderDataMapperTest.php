<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Enum\Order\GroupStatus;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Service\OrderDataMapper;

/**
 * @internal
 */
#[CoversClass(OrderDataMapper::class)]
final class OrderDataMapperTest extends TestCase
{
    private OrderDataMapper $mapper;

    /** @var MockObject&CategoryRepository */
    private MockObject $categoryRepository;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->mapper = new OrderDataMapper($this->categoryRepository);
    }

    public function testMapToOrderWithFullData(): void
    {
        $order = new Order();
        $item = [
            'order_sn' => 'TEST123',
            'support_nationwide_warranty' => true,
            'free_sf' => false,
            'return_freight_payer' => true,
            'delivery_one_day' => false,
            'is_stock_out' => false,
            'is_lucky_flag' => true,
            'invoice_status' => false,
            'only_support_replace' => false,
            'duoduo_wholesale' => false,
            'is_pre_sale' => true,
            'group_status' => 1,
            'order_status' => 1,
            'item_list' => [['name' => 'test']],
            'discount_amount' => 10.5,
            'platform_discount' => 5.0,
            'remark' => 'Test remark',
            'created_time' => '2024-01-01 12:00:00',
            'pay_time' => '2024-01-01 12:05:00',
        ];

        $this->mapper->mapToOrder($order, $item);

        self::assertTrue($order->isSupportNationwideWarranty());
        self::assertFalse($order->isFreeSf());
        self::assertTrue($order->isReturnFreightPayer());
        self::assertFalse($order->isDeliveryOneDay());
        self::assertSame(GroupStatus::tryFrom(1), $order->getGroupStatus());
        self::assertSame(OrderStatus::tryFrom(1), $order->getOrderStatus());
        self::assertSame('Test remark', $order->getRemark());
    }

    public function testMapToOrderWithMinimalData(): void
    {
        $order = new Order();
        $item = [];

        $this->mapper->mapToOrder($order, $item);

        self::assertNotNull($order->getContext());
    }

    public function testMapToOrderWithCategory(): void
    {
        $order = new Order();
        $category = $this->createMock(Category::class);

        $this->categoryRepository
            ->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($category)
        ;

        $item = ['cat_id_1' => 123];

        $this->mapper->mapToOrder($order, $item);

        self::assertSame($category, $order->getCategory());
    }

    public function testMapToOrderWithInvalidCategoryId(): void
    {
        $order = new Order();

        $this->categoryRepository
            ->expects(self::once())
            ->method('find')
            ->with(999)
            ->willReturn(null)
        ;

        $item = ['cat_id_1' => 999];

        $this->mapper->mapToOrder($order, $item);

        self::assertNull($order->getCategory());
    }

    public function testMapToOrderUpdatesContext(): void
    {
        $order = new Order();
        $item = ['order_sn' => 'TEST456'];

        $this->mapper->mapToOrder($order, $item);

        $context = $order->getContext();
        self::assertIsArray($context);
        self::assertArrayHasKey('pdd.order.list.get', $context);
        self::assertSame($item, $context['pdd.order.list.get']);
    }
}
