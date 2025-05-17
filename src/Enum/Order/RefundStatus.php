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
enum RefundStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 无售后或售后关闭 = 1;
    case 售后处理中 = 2;
    case 退款中 = 3;
    case 退款成功 = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::无售后或售后关闭 => '无售后或售后关闭',
            self::售后处理中 => '售后处理中',
            self::退款中 => '退款中',
            self::退款成功 => '退款成功',
        };
    }
}
