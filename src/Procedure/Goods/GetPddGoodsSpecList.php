<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Param\Goods\GetPddGoodsSpecListParam;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\CategoryService;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '商品属性类目接口')]
#[MethodExpose(method: 'GetPddGoodsSpecList')]
#[MethodTag(name: '拼多多API')]
final class GetPddGoodsSpecList extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService,
    ) {
    }

    /**
     * @phpstan-param GetPddGoodsSpecListParam $param
     */
    public function execute(GetPddGoodsSpecListParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException(ErrorMessageConstants::MALL_NOT_FOUND);
        }

        $category = $this->categoryRepository->find($param->categoryId);
        if (null === $category) {
            throw new ApiException(ErrorMessageConstants::CATEGORY_NOT_FOUND);
        }
        $this->categoryService->syncSpecList($mall, $category);

        $list = [];
        foreach ($category->getSpecs() as $spec) {
            $list[] = [
                'parent_spec_id' => $spec->getId(),
                'parent_spec_name' => $spec->getName(),
            ];
        }

        return new ArrayResult([
            'list' => $list,
        ]);
    }
}
