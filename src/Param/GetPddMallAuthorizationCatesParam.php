<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetPddMallAuthorizationCatesParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: '默认值=0,值=0时为顶点cat_id,通过树顶级节点获取一级类目')]
        public int $parentCatId = 0,
    ) {
    }
}
