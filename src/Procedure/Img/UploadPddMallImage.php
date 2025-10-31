<?php

namespace PinduoduoApiBundle\Procedure\Img;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\UploadService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc(summary: '上传PDD图片')]
#[MethodExpose(method: 'UploadPddMallImage')]
#[MethodTag(name: '拼多多API')]
#[Log]
class UploadPddMallImage extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly UploadService $uploadService,
    ) {
    }

    #[MethodParam(description: '店铺ID')]
    public string $mallId;

    #[MethodParam(description: '图片URL')]
    public string $imgUrl;

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if (null === $mall) {
            throw new ApiException('找不到店铺信息');
        }

        $img = $this->uploadService->uploadImage($mall, $this->imgUrl);

        return [
            'url' => $img->getUrl(),
        ];
    }
}
