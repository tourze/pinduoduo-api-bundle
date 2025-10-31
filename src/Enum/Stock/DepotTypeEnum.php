<?php

namespace PinduoduoApiBundle\Enum\Stock;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DepotTypeEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case SELF_BUILT = 1;      // 自建仓
    case AGENT = 2;           // 代仓
    case THIRD_PARTY = 3;     // 第三方仓
    case VIRTUAL = 4;         // 虚拟仓
    case CROSS_BORDER = 5;    // 跨境仓
    case BONDED = 6;          // 保税仓
    case OVERSEAS = 7;        // 海外仓

    public function getLabel(): string
    {
        return match ($this) {
            self::SELF_BUILT => '自建仓',
            self::AGENT => '代仓',
            self::THIRD_PARTY => '第三方仓',
            self::VIRTUAL => '虚拟仓',
            self::CROSS_BORDER => '跨境仓',
            self::BONDED => '保税仓',
            self::OVERSEAS => '海外仓',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::SELF_BUILT => 'success',
            self::AGENT => 'info',
            self::THIRD_PARTY => 'warning',
            self::VIRTUAL => 'light',
            self::CROSS_BORDER => 'primary',
            self::BONDED => 'secondary',
            self::OVERSEAS => 'danger',
        };
    }
}
