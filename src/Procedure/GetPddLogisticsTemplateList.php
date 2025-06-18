<?php

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc('获取拼多多店铺的运费模板')]
#[MethodExpose('GetPddLogisticsTemplateList')]
class GetPddLogisticsTemplateList extends LockableProcedure
{
    #[MethodParam('店铺ID')]
    public string $mallId;

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
    ) {
    }

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if ($mall === null) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            return $this->sdkService->request($mall, 'pdd.goods.logistics.template.get')['goods_logistics_template_get_response'];
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }
}
