<?php

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.cat.rule.get
 */
#[MethodDoc('获取拼多多店铺的cat rules')]
#[MethodExpose('GetPddMallCatRule')]
class GetPddMallCatRule extends BaseProcedure
{
    #[MethodParam('店铺ID')]
    public string $mallId;

    #[MethodParam('门店ID')]
    public string $categoryId;

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SdkService $sdkService,
    ) {
    }

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (!$mall) {
            throw new ApiException('找不到店铺信息');
        }

        $category = $this->categoryRepository->find($this->categoryId);
        if (!$category) {
            throw new ApiException('找不到目录');
        }

        try {
            $result = $this->sdkService->request($mall, 'pdd.goods.cat.rule.get', [
                'cat_id' => $category->getId(),
            ]);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }

        return $result['cat_rule_get_response'];
    }
}
