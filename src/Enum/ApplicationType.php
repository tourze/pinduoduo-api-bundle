<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 电商软件服务商 - 应用类型
 *
 * @see https://open.pinduoduo.com/application/app/create-list
 */
enum ApplicationType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case 推广优化 = '推广优化';
    case 短信服务 = '短信服务';
    case 打单 = '打单';
    case 进销存 = '进销存';
    case 商品优化分析 = '商品优化分析';
    case 搬家上货 = '搬家上货';
    case 电子面单 = '电子面单';
    case 企业ERP = '企业ERP';
    case 仓储管理系统 = '仓储管理系统';
    case 订单处理 = '订单处理';
    case 快团团 = '快团团';
    case 跨境企业ERP报关版 = '跨境企业ERP报关版';

    public function getLabel(): string
    {
        return match ($this) {
            self::推广优化 => '推广优化',
            self::短信服务 => '短信服务',
            self::打单 => '打单',
            self::进销存 => '进销存',
            self::商品优化分析 => '商品优化分析',
            self::搬家上货 => '搬家上货',
            self::电子面单 => '电子面单',
            self::企业ERP => '企业ERP',
            self::仓储管理系统 => '仓储管理系统',
            self::订单处理 => '订单处理',
            self::快团团 => '快团团',
            self::跨境企业ERP报关版 => '跨境企业ERP报关版',
        };
    }
}
