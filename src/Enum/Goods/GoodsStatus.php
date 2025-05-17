<?php

namespace PinduoduoApiBundle\Enum\Goods;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 商品状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.detail.get
 */
enum GoodsStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case Up = 1;
    case Down = 2;
    case Out = 3;
    case Deleted = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::Up => '上架',
            self::Down => '下架',
            self::Out => '售罄',
            self::Deleted => '已删除',
        };
    }
}
