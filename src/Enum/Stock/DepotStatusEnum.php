<?php

namespace PinduoduoApiBundle\Enum\Stock;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DepotStatusEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case ACTIVE = 1;          // 正常
    case DISABLED = 2;        // 停用
    case PENDING = 3;         // 待审核
    case REJECTED = 4;        // 审核不通过
    case MAINTENANCE = 5;     // 维护中
    case FULL = 6;           // 已满仓
    case CLOSED = 7;         // 已关闭

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => '正常',
            self::DISABLED => '停用',
            self::PENDING => '待审核',
            self::REJECTED => '审核不通过',
            self::MAINTENANCE => '维护中',
            self::FULL => '已满仓',
            self::CLOSED => '已关闭',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::DISABLED => 'secondary',
            self::PENDING => 'warning',
            self::REJECTED => 'danger',
            self::MAINTENANCE => 'info',
            self::FULL => 'warning',
            self::CLOSED => 'dark',
        };
    }
}
