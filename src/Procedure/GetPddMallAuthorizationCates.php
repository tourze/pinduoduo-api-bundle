<?php

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.authorization.cats
 */
#[MethodDoc(summary: '获取当前授权商家可发布的商品类目信息')]
#[MethodExpose(method: 'GetPddMallAuthorizationCates')]
class GetPddMallAuthorizationCates extends BaseProcedure
{
    #[MethodParam(description: '默认值=0，值=0时为顶点cat_id,通过树顶级节点获取一级类目')]
    public int $parentCatId = 0;

    #[MethodParam(description: '店铺ID')]
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
            return $this->sdkService->request($mall, 'pdd.goods.authorization.cats', [
                'parent_cat_id' => $this->parentCatId,
            ])['goods_auth_cats_get_response']['goods_cats_list'];
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }
}
