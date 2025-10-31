<?php

namespace PinduoduoApiBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum CostType: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;
    case ByAmount = 0;
    case ByWeight = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::ByAmount => 'ByAmount',
            self::ByWeight => 'ByWeight',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ByAmount => 'primary',
            self::ByWeight => 'info',
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
