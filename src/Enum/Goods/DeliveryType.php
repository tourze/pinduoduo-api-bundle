<?php

namespace PinduoduoApiBundle\Enum\Goods;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发货方式
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.detail.get
 */
enum DeliveryType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NoDelivery = 0;
    case HasDelivery = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::NoDelivery => '无物流发货',
            self::HasDelivery => '有物流发货',
        };
    }
}
