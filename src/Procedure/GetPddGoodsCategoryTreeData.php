<?php

namespace PinduoduoApiBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Yiisoft\Json\Json;

#[MethodDoc(summary: '获取拼多多商品类目（公开数据）')]
#[MethodExpose(method: 'GetPddGoodsCategoryTreeData')]
#[MethodTag(name: '拼多多API')]
class GetPddGoodsCategoryTreeData extends BaseProcedure
{
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $json = file_get_contents(__DIR__ . '/category.json');
        if (false === $json) {
            throw new \RuntimeException('Failed to read category.json file');
        }

        $decoded = Json::decode($json);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON format in category.json');
        }

        /** @var array<string, mixed> $result */
        $result = [];
        foreach ($decoded as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }
}
