<?php

namespace PinduoduoApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Exception\UploadFailedException;
use PinduoduoApiBundle\Repository\UploadImgRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\TempFileBundle\Service\TemporaryFileService;

#[Autoconfigure(public: true)]
class UploadService
{
    public function __construct(
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly TemporaryFileService $temporaryFileService,
        private readonly UploadImgRepository $imgRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 上传图片到远程
     */
    public function uploadImage(Mall $mall, string $file): UploadImg
    {
        /** @var UploadImg|null $img */
        $img = $this->imgRepository->findOneBy([
            'mall' => $mall,
            'file' => $file,
        ]);
        if (null === $img) {
            $img = new UploadImg();
            $img->setMall($mall);
            $img->setFile($file);
        }

        $url = $img->getUrl();
        $imgFile = $img->getFile();

        if (null === $url || '' === $url) {
            if (null === $imgFile) {
                throw new UploadFailedException('图片文件路径为空');
            }

            $localFile = $this->temporaryFileService->generateTemporaryFileName('pdd');
            file_put_contents($localFile, file_get_contents($imgFile));
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.img.upload', [
                'file' => $localFile,
            ]);
            // array:1 [
            //  "goods_img_upload_response" => array:2 [
            //    "request_id" => "17157431736782793"
            //    "url" => "https://img.pddpic.com/open-gw/2024-05-15/c98022b4-f4ea-4025-9d76-ef1e7f5c119b.jpeg"
            //  ]
            // ]
            if (!isset($response['url']) || !is_string($response['url'])) {
                throw new UploadFailedException('图片上传失败');
            }
            $img->setUrl($response['url']);
        }
        $this->entityManager->persist($img);
        $this->entityManager->flush();

        return $img;
    }
}
