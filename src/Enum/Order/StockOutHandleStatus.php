<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 缺货处理状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum StockOutHandleStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 无缺货处理 = -1;
    case 缺货待处理 = 0;
    case 缺货已处理 = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::无缺货处理 => '预约配送',
            self::缺货待处理 => '缺货待处理',
            self::缺货已处理 => '缺货已处理',
        };
    }
}
