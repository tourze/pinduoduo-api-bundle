<?php

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '商品属性类目接口')]
#[MethodExpose(method: 'GetPddGoodsMallSpecValue')]
#[MethodTag(name: '拼多多API')]
class GetPddGoodsMallSpecValue extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    #[MethodParam(description: '店铺ID')]
    public string $mallId;

    #[MethodParam(description: '拼多多标准规格ID')]
    public string $parentSpecId;

    #[MethodParam(description: '商家编辑的规格值')]
    public string $specName;

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        return $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.spec.id.get', [
            'parent_spec_id' => $this->parentSpecId,
            'spec_name' => $this->specName,
        ]);
    }
}
