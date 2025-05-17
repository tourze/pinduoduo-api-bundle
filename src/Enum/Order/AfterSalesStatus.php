<?php

namespace PinduoduoApiBundle\Enum\Order;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 售后状态
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.order.information.get
 */
enum AfterSalesStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 无售后 = 0;
    case 买家申请退款，待商家处理 = 2;
    case 退货退款，待商家处理 = 3;
    case 商家同意退款，退款中 = 4;
    case 平台同意退款，退款中 = 5;
    case 驳回退款，待买家处理 = 6;
    case 已同意退货退款，待用户发货 = 7;
    case 平台处理中 = 8;
    case 平台拒绝退款，退款关闭 = 9;
    case 退款成功 = 10;
    case 买家撤销 = 11;
    case 买家逾期未处理，退款失败 = 12;
    case 买家逾期，超过有效期 = 13;
    case 换货补寄待商家处理 = 14;
    case 换货补寄待用户处理 = 15;
    case 换货补寄成功 = 16;
    case 换货补寄失败 = 17;
    case 换货补寄待用户确认完成 = 18;
    case 待商家同意维修 = 21;
    case 待用户确认发货 = 22;
    case 维修关闭 = 24;
    case 维修成功 = 25;
    case 待用户确认收货 = 27;
    case 已同意拒收退款，待用户拒收 = 31;
    case 补寄待商家发货 = 32;
    case 同意召回后退款，待商家召回 = 33;

    public function getLabel(): string
    {
        return match ($this) {
            self::无售后 => '无售后',
            self::买家申请退款，待商家处理 => '买家申请退款，待商家处理',
            self::退货退款，待商家处理 => '退货退款，待商家处理',
            self::商家同意退款，退款中 => '商家同意退款，退款中',
            self::平台同意退款，退款中 => '平台同意退款，退款中',
            self::驳回退款，待买家处理 => '驳回退款，待买家处理',
            self::已同意退货退款，待用户发货 => '已同意退货退款，待用户发货',
            self::平台处理中 => '平台处理中',
            self::平台拒绝退款，退款关闭 => '平台拒绝退款，退款关闭',
            self::退款成功 => '退款成功',
            self::买家撤销 => '买家撤销',
            self::买家逾期未处理，退款失败 => '买家逾期未处理，退款失败',
            self::买家逾期，超过有效期 => '买家逾期，超过有效期',
            self::换货补寄待商家处理 => '换货补寄待商家处理',
            self::换货补寄待用户处理 => '换货补寄待用户处理',
            self::换货补寄成功 => '换货补寄成功',
            self::换货补寄失败 => '换货补寄失败',
            self::换货补寄待用户确认完成 => '换货补寄待用户确认完成',
            self::待商家同意维修 => '待商家同意维修',
            self::待用户确认发货 => '待用户确认发货',
            self::维修关闭 => '维修关闭',
            self::维修成功 => '维修成功',
            self::待用户确认收货 => '待用户确认收货',
            self::已同意拒收退款，待用户拒收 => '已同意拒收退款，待用户拒收',
            self::补寄待商家发货 => '补寄待商家发货',
            self::同意召回后退款，待商家召回 => '同意召回后退款，待商家召回',
        };
    }
}
