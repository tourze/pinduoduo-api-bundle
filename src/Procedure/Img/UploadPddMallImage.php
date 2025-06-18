<?php

namespace PinduoduoApiBundle\Procedure\Img;

use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\UploadService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc('上传PDD图片')]
#[MethodExpose('UploadPddMallImage')]
#[Log]
class UploadPddMallImage extends LockableProcedure
{
    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly UploadService $uploadService,
    ) {
    }

    #[MethodParam('店铺ID')]
    public string $mallId;

    #[MethodParam('图片URL')]
    public string $imgUrl;

    public function execute(): array
    {
        $mall = $this->mallRepository->find($this->mallId);
        if ($mall === null) {
            throw new ApiException('找不到店铺信息');
        }

        $img = $this->uploadService->uploadImage($mall, $this->imgUrl);

        return [
            'url' => $img->getUrl(),
        ];
    }
}
