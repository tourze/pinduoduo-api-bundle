<?php

namespace PinduoduoApiBundle\Enum\Stock;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum StockWareTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NORMAL = 1;      // 普通货品
    case COMBINED = 2;    // 组合货品
    case VIRTUAL = 3;     // 虚拟货品

    public function getLabel(): string
    {
        return match($this) {
            self::NORMAL => '普通货品',
            self::COMBINED => '组合货品',
            self::VIRTUAL => '虚拟货品',
        };
    }
}
