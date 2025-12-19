<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Exception;

final class ErrorMessageConstants
{
    public const MALL_NOT_FOUND = '找不到店铺信息';
    public const CATEGORY_NOT_FOUND = '找不到分类信息';
    public const DIRECTORY_NOT_FOUND = '找不到目录';

    private function __construct()
    {
    }
}