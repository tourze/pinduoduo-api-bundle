<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ExecPddApiParam implements RpcParamInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        #[MethodParam(description: '店铺ID')]
        public string $mallId,

        #[MethodParam(description: 'API')]
        public string $api,

        #[MethodParam(description: '参数')]
        public array $params = [],
    ) {
    }
}
