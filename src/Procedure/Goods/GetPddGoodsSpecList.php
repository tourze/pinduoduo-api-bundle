<?php

namespace PinduoduoApiBundle\Procedure\Goods;

use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\CategoryService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc('商品属性类目接口')]
#[MethodExpose('GetPddGoodsSpecList')]
class GetPddGoodsSpecList extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService,
    ) {
    }

    #[MethodParam('店铺ID')]
    public string $mallId;

    #[MethodParam('分类ID')]
    public string $categoryId;

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if ($mall === null) {
            throw new ApiException('找不到店铺信息');
        }

        $category = $this->categoryRepository->find($this->categoryId);
        if ($category === null) {
            throw new ApiException('找不到分类信息');
        }
        $this->categoryService->syncSpecList($mall, $category);

        $result = [];
        foreach ($category->getSpecs() as $spec) {
            $result[] = [
                'parent_spec_id' => $spec->getId(),
                'parent_spec_name' => $spec->getName(),
            ];
        }

        return $result;
    }
}
