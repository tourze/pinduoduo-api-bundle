<?php

namespace PinduoduoApiBundle\Enum\Stock;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DepotBusinessTypeEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case NORMAL = 1;          // 普通仓
    case RETURN = 2;          // 退货仓
    case TEMPORARY = 3;       // 临时仓
    case TRANSFER = 4;        // 中转仓
    case DISTRIBUTION = 5;    // 分拨中心
    case COLLECTION = 6;      // 集货仓
    case DELIVERY = 7;        // 配送中心

    public function getLabel(): string
    {
        return match ($this) {
            self::NORMAL => '普通仓',
            self::RETURN => '退货仓',
            self::TEMPORARY => '临时仓',
            self::TRANSFER => '中转仓',
            self::DISTRIBUTION => '分拨中心',
            self::COLLECTION => '集货仓',
            self::DELIVERY => '配送中心',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::NORMAL => 'success',
            self::RETURN => 'warning',
            self::TEMPORARY => 'info',
            self::TRANSFER => 'primary',
            self::DISTRIBUTION => 'danger',
            self::COLLECTION => 'secondary',
            self::DELIVERY => 'success',
        };
    }
}
