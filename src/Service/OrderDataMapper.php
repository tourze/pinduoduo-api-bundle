<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Service;

use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use PinduoduoApiBundle\Enum\Order\GroupStatus;
use PinduoduoApiBundle\Enum\Order\MktBizType;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Enum\Order\PayType;
use PinduoduoApiBundle\Enum\Order\RefundStatus;
use PinduoduoApiBundle\Enum\Order\RiskControlStatus;
use PinduoduoApiBundle\Enum\Order\ShippingType;
use PinduoduoApiBundle\Enum\Order\StockOutHandleStatus;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;

final class OrderDataMapper
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @param array<mixed> $item
     */
    public function mapToOrder(Order $order, array $item): void
    {
        $this->updateOrderContext($order, $item);
        $this->updateOrderBooleanFields($order, $item);
        $this->updateOrderEnumFields($order, $item);
        $this->updateOrderScalarFields($order, $item);
        $this->updateOrderDateFields($order, $item);
        $this->updateOrderCategory($order, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderContext(Order $order, array $item): void
    {
        $context = $order->getContext() ?? [];
        $context['pdd.order.list.get'] = $item;
        $order->setContext($context);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderBooleanFields(Order $order, array $item): void
    {
        $order->setSupportNationwideWarranty((bool) $item['support_nationwide_warranty']);
        $order->setFreeSf((bool) $item['free_sf']);
        $order->setReturnFreightPayer((bool) $item['return_freight_payer']);
        $order->setDeliveryOneDay((bool) $item['delivery_one_day']);
        $order->setStockOut((bool) $item['is_stock_out']);
        $order->setLuckyFlag((bool) $item['is_lucky_flag']);
        $order->setInvoiceStatus((bool) $item['invoice_status']);
        $order->setOnlySupportReplace((bool) $item['only_support_replace']);
        $order->setDuoduoWholesale((bool) $item['duoduo_wholesale']);
        $order->setPreSale((bool) $item['is_pre_sale']);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderEnumFields(Order $order, array $item): void
    {
        $this->updateOrderStatusEnums($order, $item);
        $this->updateOrderPaymentEnums($order, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderStatusEnums(Order $order, array $item): void
    {
        $this->setEnumField($item, 'group_status', fn ($value) => $order->setGroupStatus(GroupStatus::tryFrom($value)));
        $this->setEnumField($item, 'order_status', fn ($value) => $order->setOrderStatus(OrderStatus::tryFrom($value)));
        $this->setEnumField($item, 'risk_control_status', fn ($value) => $order->setRiskControlStatus(RiskControlStatus::tryFrom($value)));
        $this->setEnumField($item, 'refund_status', fn ($value) => $order->setRefundStatus(RefundStatus::tryFrom($value)));
        $this->setEnumField($item, 'confirm_status', fn ($value) => $order->setConfirmStatus(ConfirmStatus::tryFrom($value)));
        $this->setEnumField($item, 'stock_out_handle_status', fn ($value) => $order->setStockOutHandleStatus(StockOutHandleStatus::tryFrom($value)));
    }

    /**
     * @param array<mixed> $item
     * @param callable(int|string): void $setter
     */
    private function setEnumField(array $item, string $key, callable $setter): void
    {
        if (!isset($item[$key])) {
            return;
        }

        $value = $item[$key];
        if (is_int($value) || is_string($value)) {
            $setter($value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderPaymentEnums(Order $order, array $item): void
    {
        $this->setEnumField($item, 'mkt_biz_type', fn ($value) => $order->setMktBizType(MktBizType::tryFrom($value)));
        $this->setEnumField($item, 'shipping_type', function ($value) use ($order) {
            $shippingType = ShippingType::tryFrom($value);
            $order->setShippingType($shippingType?->value);
        });
        $this->setEnumField($item, 'pay_type', fn ($value) => $order->setPayType(PayType::tryFrom($value)));
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderScalarFields(Order $order, array $item): void
    {
        $this->updateOrderListFields($order, $item);
        $this->updateOrderAmountFields($order, $item);
        $this->updateOrderMiscFields($order, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderListFields(Order $order, array $item): void
    {
        if (isset($item['item_list']) && is_array($item['item_list'])) {
            $order->setItemList($item['item_list']);
        }
        if (isset($item['card_info_list']) && is_array($item['card_info_list'])) {
            $order->setCardInfoList($item['card_info_list']);
        }
        if (isset($item['gift_list']) && is_array($item['gift_list'])) {
            $order->setGiftList($item['gift_list']);
        }
        if (isset($item['order_tag_list']) && is_array($item['order_tag_list'])) {
            $order->setOrderTagList($item['order_tag_list']);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderAmountFields(Order $order, array $item): void
    {
        $this->setDiscountAmount($order, $item);
        $this->setPlatformDiscount($order, $item);
        $this->setCapitalFreeDiscount($order, $item);
        $this->setOrderChangeAmount($order, $item);
        $this->setGoodsAmount($order, $item);
        $this->setPayAmount($order, $item);
        $this->setSellerDiscount($order, $item);
        $this->setPostage($order, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function setDiscountAmount(Order $order, array $item): void
    {
        if (!isset($item['discount_amount'])) {
            return;
        }

        $value = $item['discount_amount'];
        if (is_float($value) || is_int($value)) {
            $order->setDiscountAmount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setPlatformDiscount(Order $order, array $item): void
    {
        if (!isset($item['platform_discount'])) {
            return;
        }

        $value = $item['platform_discount'];
        if (is_float($value) || is_int($value)) {
            $order->setPlatformDiscount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setCapitalFreeDiscount(Order $order, array $item): void
    {
        if (isset($item['capital_free_discount']) && is_string($item['capital_free_discount'])) {
            $order->setCapitalFreeDiscount($item['capital_free_discount']);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setOrderChangeAmount(Order $order, array $item): void
    {
        if (!isset($item['order_change_amount'])) {
            return;
        }

        $value = $item['order_change_amount'];
        if (is_float($value) || is_int($value)) {
            $order->setOrderChangeAmount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setGoodsAmount(Order $order, array $item): void
    {
        if (!isset($item['goods_amount'])) {
            return;
        }

        $value = $item['goods_amount'];
        if (is_float($value) || is_int($value)) {
            $order->setGoodsAmount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setPayAmount(Order $order, array $item): void
    {
        if (!isset($item['pay_amount'])) {
            return;
        }

        $value = $item['pay_amount'];
        if (is_float($value) || is_int($value)) {
            $order->setPayAmount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setSellerDiscount(Order $order, array $item): void
    {
        if (!isset($item['seller_discount'])) {
            return;
        }

        $value = $item['seller_discount'];
        if (is_float($value) || is_int($value)) {
            $order->setSellerDiscount((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function setPostage(Order $order, array $item): void
    {
        if (!isset($item['postage'])) {
            return;
        }

        $value = $item['postage'];
        if (is_float($value) || is_int($value)) {
            $order->setPostage((float) $value);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderMiscFields(Order $order, array $item): void
    {
        if (isset($item['service_fee_detail']) && is_array($item['service_fee_detail'])) {
            $order->setServiceFeeDetail($item['service_fee_detail']);
        }
        if (isset($item['remark']) && is_string($item['remark'])) {
            $order->setRemark($item['remark']);
        }
        if (isset($item['tracking_number']) && is_string($item['tracking_number'])) {
            $order->setTrackingNumber($item['tracking_number']);
        }
        if (isset($item['buyer_memo']) && is_string($item['buyer_memo'])) {
            $order->setBuyerMemo($item['buyer_memo']);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderDateFields(Order $order, array $item): void
    {
        $this->setDateTimeField($item, 'created_time', fn ($dt) => $order->setCreateTime($dt));
        $this->setDateTimeField($item, 'last_ship_time', fn ($dt) => $order->setLastShipTime($dt));
        $this->setDateTimeField($item, 'receive_time', fn ($dt) => $order->setReceiveTime($dt));
        $this->setDateTimeField($item, 'pay_time', fn ($dt) => $order->setPayTime($dt));
        $this->setDateTimeField($item, 'updated_at', fn ($dt) => $order->setUpdateTime($dt));
        $this->setDateTimeField($item, 'shipping_time', fn ($dt) => $order->setShippingTime($dt));
        $this->setDateTimeField($item, 'confirm_time', fn ($dt) => $order->setConfirmTime($dt));
    }

    /**
     * @param array<mixed> $item
     * @param callable(\DateTimeImmutable|null): void $setter
     */
    private function setDateTimeField(array $item, string $key, callable $setter): void
    {
        if (!isset($item[$key]) || !is_string($item[$key])) {
            return;
        }

        $datetime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item[$key]);
        $setter(false !== $datetime ? $datetime : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateOrderCategory(Order $order, array $item): void
    {
        $category = null;
        $checkKeys = ['cat_id_4', 'cat_id_3', 'cat_id_2', 'cat_id_1'];

        foreach ($checkKeys as $key) {
            if (null !== $category) {
                break;
            }
            if (isset($item[$key]) && 0 !== $item[$key]) {
                $foundCategory = $this->categoryRepository->find($item[$key]);
                if ($foundCategory instanceof Category) {
                    $category = $foundCategory;
                }
            }
        }

        $order->setCategory($category);
    }
}
