<?php

namespace PinduoduoApiBundle\Enum\Order;

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
enum ShippingType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case Appointment = 1;
    case OneHour = 2;
    case Customer = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Appointment => '预约配送',
            self::OneHour => '1小时达',
            self::Customer => '消费者预约送达',
        };
    }
}
