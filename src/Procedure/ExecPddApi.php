<?php

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc('执行PDD-API')]
#[MethodExpose('ExecPddApi')]
class ExecPddApi extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
    ) {
    }

    #[MethodParam('店铺ID')]
    public string $mallId;

    #[MethodParam('API')]
    public string $api;

    #[MethodParam('参数')]
    public array $params = [];

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (!$mall) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            return $this->sdkService->request($mall, $this->api, $this->params);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), $exception->getCode(), previous: $exception);
        }
    }
}
