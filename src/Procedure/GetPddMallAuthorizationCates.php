<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Param\GetPddMallAuthorizationCatesParam;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.authorization.cats
 */
#[MethodDoc(summary: '获取当前授权商家可发布的商品类目信息')]
#[MethodExpose(method: 'GetPddMallAuthorizationCates')]
#[MethodTag(name: '拼多多API')]
final class GetPddMallAuthorizationCates extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    /**
     * @phpstan-param GetPddMallAuthorizationCatesParam $param
     */
    public function execute(GetPddMallAuthorizationCatesParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        try {
            $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.authorization.cats', [
                'parent_cat_id' => $param->parentCatId,
            ]);

            $catsList = $result['goods_cats_list'] ?? [];

            /** @var ArrayResult<array<string, mixed>> */
            return new ArrayResult(is_array($catsList) ? $catsList : []);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }
}
