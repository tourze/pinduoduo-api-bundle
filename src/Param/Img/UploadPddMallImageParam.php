<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param\Img;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class UploadPddMallImageParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: '图片URL')]
        public string $imgUrl,
    ) {
    }
}
