<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Param\Goods\GetPddGoodsMallSpecValueParam;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '商品属性类目接口')]
#[MethodExpose(method: 'GetPddGoodsMallSpecValue')]
#[MethodTag(name: '拼多多API')]
final class GetPddGoodsMallSpecValue extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    /**
     * @phpstan-param GetPddGoodsMallSpecValueParam $param
     */
    public function execute(GetPddGoodsMallSpecValueParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException(ErrorMessageConstants::MALL_NOT_FOUND);
        }

        return $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.spec.id.get', [
            'parent_spec_id' => $param->parentSpecId,
            'spec_name' => $param->specName,
        ]);
    }
}
