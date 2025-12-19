<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param\Goods;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetPddGoodsMallSpecValueParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: '拼多多标准规格ID')]
        public string $parentSpecId,

        #[MethodParam(description: '商家编辑的规格值')]
        public string $specName,
    ) {
    }
}
