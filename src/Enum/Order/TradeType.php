<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 订单类型
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum TradeType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 普通订单 = 0;
    case 定金订单 = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::普通订单 => '普通订单',
            self::定金订单 => '定金订单',
        };
    }
}
