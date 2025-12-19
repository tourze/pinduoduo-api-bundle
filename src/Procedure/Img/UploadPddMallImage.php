<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Procedure\Img;

use PinduoduoApiBundle\Param\Img\UploadPddMallImageParam;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\UploadService;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc(summary: '上传PDD图片')]
#[MethodExpose(method: 'UploadPddMallImage')]
#[MethodTag(name: '拼多多API')]
#[Log]
final class UploadPddMallImage extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly UploadService $uploadService,
    ) {
    }

    /**
     * @phpstan-param UploadPddMallImageParam $param
     */
    public function execute(UploadPddMallImageParam|RpcParamInterface $param): ArrayResult
    {
        $mall = $this->mallRepository->find($param->mallId);
        if (null === $mall) {
            throw new ApiException(ErrorMessageConstants::MALL_NOT_FOUND);
        }

        $img = $this->uploadService->uploadImage($mall, $param->imgUrl);

        return new ArrayResult([
            'url' => $img->getUrl(),
        ]);
    }
}
