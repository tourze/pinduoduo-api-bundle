<?php

namespace PinduoduoApiBundle\Enum\Goods;

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
enum GoodsType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 国内普通商品 = 1;
    case 进口 = 2;
    case 国外海淘 = 3;
    case 直邮 = 4;
    case 流量 = 5;
    case 话费 = 6;
    case 优惠券 = 7;
    case QQ充值 = 8;
    case 加油卡 = 9;
    case CC行邮 = 18;

    public function getLabel(): string
    {
        return match ($this) {
            self::国内普通商品 => '国内普通商品',
            self::进口 => '进口',
            self::国外海淘 => '国外海淘',
            self::直邮 => '直邮',
            self::流量 => '流量',
            self::话费 => '话费',
            self::优惠券 => '优惠券',
            self::QQ充值 => 'QQ充值',
            self::加油卡 => '加油卡',
            self::CC行邮 => 'CC行邮',
        };
    }
}
