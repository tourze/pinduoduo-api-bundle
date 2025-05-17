<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 商品类型
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.information.get
 */
enum CostType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ByAmount = 0;
    case ByWeight = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::ByAmount => '按件计费',
            self::ByWeight => '按重量计费',
        };
    }
}
