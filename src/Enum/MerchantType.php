<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 店铺类型
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.mall.info.get
 */
enum MerchantType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 个人 = 1;
    case 企业 = 2;
    case 旗舰店 = 3;
    case 专卖店 = 4;
    case 专营店 = 5;
    case 普通店 = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::个人 => '个人',
            self::企业 => '企业',
            self::旗舰店 => '旗舰店',
            self::专卖店 => '专卖店',
            self::专营店 => '专营店',
            self::普通店 => '普通店',
        };
    }
}
