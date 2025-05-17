<?php

namespace PinduoduoApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use FileSystemBundle\Service\TemporaryFileService;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Repository\UploadImgRepository;

class UploadService
{
    public function __construct(
        private readonly SdkService $sdkService,
        private readonly TemporaryFileService $temporaryFileService,
        private readonly UploadImgRepository $imgRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * 上传图片到远程
     */
    public function uploadImage(Mall $mall, string $file): UploadImg
    {
        $img = $this->imgRepository->findOneBy([
            'mall' => $mall,
            'file' => $file,
        ]);
        if (!$img) {
            $img = new UploadImg();
            $img->setMall($mall);
            $img->setFile($file);
        }
        if (!$img->getUrl()) {
            $localFile = $this->temporaryFileService->generateTemporaryFileName('pdd');
            file_put_contents($localFile, file_get_contents($img->getFile()));
            $response = $this->sdkService->request($mall, 'pdd.goods.img.upload', [
                'file' => $localFile,
            ]);
            // array:1 [
            //  "goods_img_upload_response" => array:2 [
            //    "request_id" => "17157431736782793"
            //    "url" => "https://img.pddpic.com/open-gw/2024-05-15/c98022b4-f4ea-4025-9d76-ef1e7f5c119b.jpeg"
            //  ]
            // ]
            if (!isset($response['goods_img_upload_response'])) {
                throw new \RuntimeException('图片上传失败');
            }
            $img->setUrl($response['goods_img_upload_response']['url']);
        }
        $this->entityManager->persist($img);
        $this->entityManager->flush();

        return $img;
    }
}
