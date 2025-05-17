<?php

namespace PinduoduoApiBundle\Enum\Stock;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DepotPriorityTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NORMAL = 1;      // 普通优先级
    case PREFERRED = 2;   // 优先
    case EXCLUSIVE = 3;   // 专属

    public function getLabel(): string
    {
        return match($this) {
            self::NORMAL => '普通',
            self::PREFERRED => '优先',
            self::EXCLUSIVE => '专属',
        };
    }
}
