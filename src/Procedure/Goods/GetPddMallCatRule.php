<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Param\Goods\GetPddMallCatRuleParam;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.cat.rule.get
 */
#[MethodDoc(summary: '获取拼多多店铺的cat rules')]
#[MethodExpose(method: 'GetPddMallCatRule')]
#[MethodTag(name: '拼多多API')]
final class GetPddMallCatRule extends BaseProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    /**
     * @phpstan-param GetPddMallCatRuleParam $param
     */
    public function execute(GetPddMallCatRuleParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException(ErrorMessageConstants::MALL_NOT_FOUND);
        }

        $category = $this->categoryRepository->find($param->categoryId);
        if (null === $category) {
            throw new ApiException(ErrorMessageConstants::DIRECTORY_NOT_FOUND);
        }

        try {
            $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.cat.rule.get', [
                'cat_id' => $category->getId(),
            ]);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }

        return new ArrayResult($result);
    }
}
