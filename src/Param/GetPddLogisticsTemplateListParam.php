<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetPddLogisticsTemplateListParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,
    ) {
    }
}
