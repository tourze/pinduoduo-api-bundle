<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 店铺身份
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.mall.info.get
 */
enum MallCharacter: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 厂商 = 0;
    case 分销商 = 1;
    case 都不是 = 2;
    case 都是 = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::厂商 => '厂商',
            self::分销商 => '分销商',
            self::都不是 => '都不是',
            self::都是 => '都是',
        };
    }
}
