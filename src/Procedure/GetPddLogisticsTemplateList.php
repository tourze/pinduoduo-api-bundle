<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure;

use PinduoduoApiBundle\Param\GetPddLogisticsTemplateListParam;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodDoc(summary: '获取拼多多店铺的运费模板')]
#[MethodExpose(method: 'GetPddLogisticsTemplateList')]
#[MethodTag(name: '拼多多API')]
final class GetPddLogisticsTemplateList extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
    }

    /**
     * @phpstan-param GetPddLogisticsTemplateListParam $param
     */
    public function execute(GetPddLogisticsTemplateListParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException(ErrorMessageConstants::MALL_NOT_FOUND);
        }

        try {
            return $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.logistics.template.get');
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }
}
