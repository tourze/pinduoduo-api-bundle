<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 店铺身份
 *
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.mall.info.get
 */
enum MallCharacter: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;
    case MANUFACTURER = 0;
    case DISTRIBUTOR = 1;
    case NEITHER = 2;
    case BOTH = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::MANUFACTURER => '厂商',
            self::DISTRIBUTOR => '分销商',
            self::NEITHER => '都不是',
            self::BOTH => '都是',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::MANUFACTURER => BadgeInterface::SUCCESS,
            self::DISTRIBUTOR => BadgeInterface::INFO,
            self::NEITHER => BadgeInterface::SECONDARY,
            self::BOTH => BadgeInterface::PRIMARY,
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: int, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }
}
