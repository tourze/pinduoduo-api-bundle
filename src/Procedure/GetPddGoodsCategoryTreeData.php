<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Param\GetPddGoodsCategoryTreeDataParam;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Yiisoft\Json\Json;

#[MethodDoc(summary: '获取拼多多商品类目（公开数据）')]
#[MethodExpose(method: 'GetPddGoodsCategoryTreeData')]
#[MethodTag(name: '拼多多API')]
final class GetPddGoodsCategoryTreeData extends BaseProcedure
{
    /**
     * @phpstan-param GetPddGoodsCategoryTreeDataParam $param
     */
    public function execute(GetPddGoodsCategoryTreeDataParam|RpcParamInterface $param): ArrayResult
    {
        $filePath = __DIR__ . '/category.json';

        // 检查文件是否存在
        if (!file_exists($filePath)) {
            throw new ApiException('类目数据文件不存在');
        }

        // 检查文件是否可读
        if (!is_readable($filePath)) {
            throw new ApiException('类目数据文件不可读');
        }

        // 检查文件大小
        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw new ApiException('无法获取类目数据文件大小');
        }

        // 限制文件大小（最大10MB）
        if ($fileSize > 10 * 1024 * 1024) {
            throw new ApiException('类目数据文件过大');
        }

        // 读取文件内容
        $json = file_get_contents($filePath);
        if (false === $json) {
            throw new ApiException('读取类目数据文件失败');
        }

        // 解析JSON
        try {
            $decoded = Json::decode($json);
        } catch (\Throwable $exception) {
            throw new ApiException('类目数据文件格式错误');
        }

        // 验证JSON内容为数组
        if (!is_array($decoded)) {
            throw new ApiException('类目数据格式错误，应为数组');
        }

        /** @var array<string, mixed> $result */
        $result = [];
        foreach ($decoded as $key => $value) {
            $result[(string) $key] = $value;
        }

        return new ArrayResult($result);
    }
}
