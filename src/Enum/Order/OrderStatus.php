<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 订单状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum OrderStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case Pending = 1;
    case Sent = 2;
    case Received = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => '待发货',
            self::Sent => '已发货待签收',
            self::Received => '已签收',
        };
    }
}
