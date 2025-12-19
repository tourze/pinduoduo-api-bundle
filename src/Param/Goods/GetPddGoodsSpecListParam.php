<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param\Goods;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetPddGoodsSpecListParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: '分类ID')]
        public string $categoryId,
    ) {
    }
}
