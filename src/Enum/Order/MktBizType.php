<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 订单审核状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum MktBizType: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case 普通订单 = 0;
    case 拼内购订单 = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::普通订单 => '普通订单',
            self::拼内购订单 => '拼内购订单',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::普通订单 => self::PRIMARY,      // 普通订单 - 主要状态
            self::拼内购订单 => self::SUCCESS,    // 拼内购订单 - 成功状态
        };
    }
}
