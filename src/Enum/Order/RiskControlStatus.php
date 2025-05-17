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
enum RiskControlStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case Normal = 0;
    case Auditing = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::Normal => '正常订单',
            self::Auditing => '审核中订单',
        };
    }
}
