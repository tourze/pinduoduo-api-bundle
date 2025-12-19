<?php

namespace PinduoduoApiBundle\Message;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
final class SyncOrderListDetailMessage
{
    /**
     * @var string 店铺ID
     */
    private string $mallId;

    public function getMallId(): string
    {
        return $this->mallId;
    }

    public function setMallId(string $mallId): void
    {
        $this->mallId = $mallId;
    }

    /**
     * @var array<mixed> 列表中拿到的单个订单信息
     */
    private array $orderInfo;

    /**
     * @return array<mixed>
     */
    public function getOrderInfo(): array
    {
        return $this->orderInfo;
    }

    /**
     * @param array<mixed> $orderInfo
     */
    public function setOrderInfo(array $orderInfo): void
    {
        $this->orderInfo = $orderInfo;
    }
}
