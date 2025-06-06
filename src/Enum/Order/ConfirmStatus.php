<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 成交状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.information.get
 */
enum ConfirmStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = 0;
    case DEAL = 1;
    case CANCEL = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => '未成交',
            self::DEAL => '已成交',
            self::CANCEL => '已取消',
        };
    }
}
