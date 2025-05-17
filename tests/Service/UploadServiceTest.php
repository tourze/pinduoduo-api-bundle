<?php

namespace PinduoduoApiBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Repository\UploadImgRepository;
use PinduoduoApiBundle\Service\SdkService;
use PinduoduoApiBundle\Service\UploadService;
use Tourze\TempFileBundle\Service\TemporaryFileService;

class UploadServiceTest extends TestCase
{
    private UploadService $uploadService;
    private MockObject|SdkService $sdkService;
    private MockObject|TemporaryFileService $temporaryFileService;
    private MockObject|UploadImgRepository $imgRepository;
    private MockObject|EntityManagerInterface $entityManager;
    
    protected function setUp(): void
    {
        $this->sdkService = $this->createMock(SdkService::class);
        $this->temporaryFileService = $this->createMock(TemporaryFileService::class);
        $this->imgRepository = $this->createMock(UploadImgRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->uploadService = new UploadService(
            $this->sdkService,
            $this->temporaryFileService,
            $this->imgRepository,
            $this->entityManager
        );
    }
    
    public function testUploadImage_existingImageWithUrl_returnsExistingImageWithoutApiCall(): void
    {
        $mall = $this->createMock(Mall::class);
        $filePath = '/path/to/image.jpg';
        
        $existingImg = new UploadImg();
        $existingImg->setMall($mall);
        $existingImg->setFile($filePath);
        $existingImg->setUrl('https://img.pddpic.com/existing.jpg');
        
        $this->imgRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'mall' => $mall,
                'file' => $filePath,
            ])
            ->willReturn($existingImg);
        
        // 由于图片已存在且有URL，不应该调用API
        $this->sdkService->expects($this->never())
            ->method('request');
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingImg);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $result = $this->uploadService->uploadImage($mall, $filePath);
        
        $this->assertSame($existingImg, $result);
        $this->assertEquals('https://img.pddpic.com/existing.jpg', $result->getUrl());
    }
    
    public function testUploadImage_existingImageWithoutUrl_uploadsImageAndUpdatesUrl(): void
    {
        $mall = $this->createMock(Mall::class);
        $filePath = '/path/to/image.jpg';
        $tempFilePath = '/tmp/pdd_temp_123456';
        
        $existingImg = new UploadImg();
        $existingImg->setMall($mall);
        $existingImg->setFile($filePath);
        // URL未设置
        
        $this->imgRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingImg);
            
        $this->temporaryFileService->expects($this->once())
            ->method('generateTemporaryFileName')
            ->with('pdd')
            ->willReturn($tempFilePath);
            
        // 模拟文件操作
        $this->mockFileOperations($filePath, $tempFilePath);
        
        $apiResponse = [
            'goods_img_upload_response' => [
                'request_id' => '123456789',
                'url' => 'https://img.pddpic.com/new-image.jpg'
            ]
        ];
        
        $this->sdkService->expects($this->once())
            ->method('request')
            ->with($mall, 'pdd.goods.img.upload', ['file' => $tempFilePath])
            ->willReturn($apiResponse);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($img) {
                return $img instanceof UploadImg && 
                       $img->getUrl() === 'https://img.pddpic.com/new-image.jpg';
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $result = $this->uploadService->uploadImage($mall, $filePath);
        
        $this->assertEquals('https://img.pddpic.com/new-image.jpg', $result->getUrl());
    }
    
    public function testUploadImage_newImage_createsNewImageAndUploads(): void
    {
        $mall = $this->createMock(Mall::class);
        $filePath = '/path/to/new-image.jpg';
        $tempFilePath = '/tmp/pdd_temp_789012';
        
        $this->imgRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->temporaryFileService->expects($this->once())
            ->method('generateTemporaryFileName')
            ->willReturn($tempFilePath);
            
        // 模拟文件操作
        $this->mockFileOperations($filePath, $tempFilePath);
        
        $apiResponse = [
            'goods_img_upload_response' => [
                'request_id' => '987654321',
                'url' => 'https://img.pddpic.com/uploaded-image.jpg'
            ]
        ];
        
        $this->sdkService->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($img) use ($mall, $filePath) {
                return $img instanceof UploadImg && 
                       $img->getMall() === $mall &&
                       $img->getFile() === $filePath &&
                       $img->getUrl() === 'https://img.pddpic.com/uploaded-image.jpg';
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $result = $this->uploadService->uploadImage($mall, $filePath);
        
        $this->assertInstanceOf(UploadImg::class, $result);
        $this->assertEquals('https://img.pddpic.com/uploaded-image.jpg', $result->getUrl());
    }
    
    public function testUploadImage_apiResponseWithoutExpectedFormat_throwsException(): void
    {
        $mall = $this->createMock(Mall::class);
        $filePath = '/path/to/image.jpg';
        $tempFilePath = '/tmp/pdd_temp_456789';
        
        $this->imgRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->temporaryFileService->expects($this->once())
            ->method('generateTemporaryFileName')
            ->willReturn($tempFilePath);
            
        // 模拟文件操作
        $this->mockFileOperations($filePath, $tempFilePath);
        
        // 返回不包含预期响应格式的结果
        $invalidResponse = [
            'error_response' => [
                'error_code' => 1001,
                'error_msg' => '上传失败'
            ]
        ];
        
        $this->sdkService->expects($this->once())
            ->method('request')
            ->willReturn($invalidResponse);
            
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('图片上传失败');
        
        $this->uploadService->uploadImage($mall, $filePath);
    }
    
    /**
     * 模拟文件操作函数
     */
    private function mockFileOperations(string $source, string $destination): void
    {
        // 由于file_put_contents和file_get_contents是PHP内置函数，
        // 我们需要使用runkit扩展修改它们的行为，但这超出了单元测试的范围
        // 在这个测试用例中，我们只是假设文件操作成功
    }
} 