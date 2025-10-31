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

#[MethodDoc(summary: '获取拼多多店铺的运费模板')]
#[MethodExpose(method: 'GetPddLogisticsTemplateList')]
#[MethodTag(name: '拼多多API')]
class GetPddLogisticsTemplateList extends LockableProcedure
{
    #[MethodParam(description: '店铺ID')]
    public string $mallId;

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            return $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.logistics.template.get');
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }
}
