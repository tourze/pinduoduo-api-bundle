<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param\Goods;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetPddMallCatRuleParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: '门店ID')]
        public string $categoryId,
    ) {
    }
}
