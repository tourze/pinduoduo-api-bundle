<?php

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc('商品属性类目接口')]
#[MethodExpose('GetPddGoodsMallSpecValue')]
class GetPddGoodsMallSpecValue extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
    ) {
    }

    #[MethodParam('店铺ID')]
    public string $mallId;

    #[MethodParam('拼多多标准规格ID')]
    public string $parentSpecId;

    #[MethodParam('商家编辑的规格值')]
    public string $specName;

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if ($mall === null) {
            throw new ApiException('找不到店铺信息');
        }

        return $this->sdkService->request($mall, 'pdd.goods.spec.id.get', [
            'parent_spec_id' => $this->parentSpecId,
            'spec_name' => $this->specName,
        ])['goods_spec_id_get_response'];
    }
}
