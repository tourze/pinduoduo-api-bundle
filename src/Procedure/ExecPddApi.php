<?php

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '执行PDD-API')]
#[MethodExpose(method: 'ExecPddApi')]
#[MethodTag(name: '拼多多API')]
class ExecPddApi extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    #[MethodParam(description: '店铺ID')]
    public string $mallId;

    #[MethodParam(description: 'API')]
    public string $api;

    /**
     * @var array<string, mixed>
     */
    #[MethodParam(description: '参数')]
    public array $params = [];

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            return $this->pinduoduoClient->requestByMall($mall, $this->api, $this->params);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), $exception->getCode(), previous: $exception);
        }
    }
}
