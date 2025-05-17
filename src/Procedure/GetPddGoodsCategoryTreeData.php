<?php

namespace PinduoduoApiBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Yiisoft\Json\Json;

#[MethodDoc('获取拼多多商品类目（公开数据）')]
#[MethodExpose('GetPddGoodsCategoryTreeData')]
class GetPddGoodsCategoryTreeData extends BaseProcedure
{
    public function execute(): array
    {
        $json = file_get_contents(__DIR__ . '/category.json');

        return Json::decode($json);
    }
}
