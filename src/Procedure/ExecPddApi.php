<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Param\ExecPddApiParam;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '执行PDD-API')]
#[MethodExpose(method: 'ExecPddApi')]
#[MethodTag(name: '拼多多API')]
final class ExecPddApi extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    /**
     * @phpstan-param ExecPddApiParam $param
     */
    public function execute(ExecPddApiParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            return $this->pinduoduoClient->requestByMall($mall, $param->api, $param->params);
        } catch (\Throwable $exception) {
            // 统一的错误消息，不暴露底层异常详情
            throw new ApiException('API请求失败', 500, previous: $exception);
        }
    }
}
