<?php

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.cat.rule.get
 */
#[MethodDoc(summary: '获取拼多多店铺的cat rules')]
#[MethodExpose(method: 'GetPddMallCatRule')]
#[MethodTag(name: '拼多多API')]
class GetPddMallCatRule extends BaseProcedure
{
    #[MethodParam(description: '店铺ID')]
    public string $mallId;

    #[MethodParam(description: '门店ID')]
    public string $categoryId;

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        $category = $this->categoryRepository->find($this->categoryId);
        if (null === $category) {
            throw new ApiException('找不到目录');
        }

        try {
            $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.cat.rule.get', [
                'cat_id' => $category->getId(),
            ]);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }

        return $result;
    }
}
