<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\BadgeInterface;
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
enum OrderStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
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

    public function getBadge(): string
    {
        return match ($this) {
            self::Pending => self::WARNING,   // 待发货 - 警告状态
            self::Sent => self::PRIMARY,      // 已发货待签收 - 主要状态
            self::Received => self::SUCCESS,  // 已签收 - 成功状态
        };
    }
}
