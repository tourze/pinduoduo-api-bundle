<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 支付方式
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
 */
enum PayType: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case QQ = 'QQ';
    case WEIXIN = 'WEIXIN';
    case ALIPAY = 'ALIPAY';
    case LIANLIANPAY = 'LIANLIANPAY';

    public function getLabel(): string
    {
        return match ($this) {
            self::QQ => 'QQ',
            self::WEIXIN => 'WEIXIN',
            self::ALIPAY => 'ALIPAY',
            self::LIANLIANPAY => 'LIANLIANPAY',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::QQ => 'info',
            self::WEIXIN => 'success',
            self::ALIPAY => 'primary',
            self::LIANLIANPAY => 'warning',
        };
    }
}
