<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 成团状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum GroupStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case Doing = 0;
    case Done = 1;
    case Failed = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Doing => '拼团中',
            self::Done => '已成团',
            self::Failed => '团失败',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::Doing => self::WARNING,     // 拼团中 - 警告状态
            self::Done => self::SUCCESS,      // 已成团 - 成功状态
            self::Failed => self::DANGER,     // 团失败 - 危险状态
        };
    }
}
