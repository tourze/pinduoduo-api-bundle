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
enum RefundStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case NO_REFUND_OR_CLOSED = 1;
    case REFUND_PROCESSING = 2;
    case REFUNDING = 3;
    case REFUND_SUCCESS = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::NO_REFUND_OR_CLOSED => '无售后或售后关闭',
            self::REFUND_PROCESSING => '售后处理中',
            self::REFUNDING => '退款中',
            self::REFUND_SUCCESS => '退款成功',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::NO_REFUND_OR_CLOSED => self::SECONDARY,   // 无售后或关闭 - 次要状态
            self::REFUND_PROCESSING => self::WARNING,       // 处理中 - 警告状态
            self::REFUNDING => self::PRIMARY,               // 退款中 - 主要状态
            self::REFUND_SUCCESS => self::SUCCESS,          // 退款成功 - 成功状态
        };
    }
}
