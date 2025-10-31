<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Repository\UploadImgRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(UploadImgRepository::class)]
#[RunTestsInSeparateProcesses]
final class UploadImgRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(UploadImgRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allUploadImgs = $repository->findAll();
        foreach ($allUploadImgs as $uploadImg) {
            $this->assertInstanceOf(UploadImg::class, $uploadImg);
            $repository->remove($uploadImg);
        }

        // 创建关联的 Mall
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for UploadImg');
        $mall->setDescription('Test mall for uploadimg data fixtures');
        self::getEntityManager()->persist($mall);

        // 添加一个测试数据以满足 DataFixtures 检查
        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('test_image.jpg');
        $uploadImg->setUrl('https://example.com/test_image.jpg');

        $repository->save($uploadImg);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindUploadImg(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('/path/to/image.jpg');
        $uploadImg->setUrl('https://example.com/image.jpg');

        $repository->save($uploadImg);

        $foundUploadImg = $repository->find($uploadImg->getId());
        $this->assertNotNull($foundUploadImg);
        $this->assertInstanceOf(UploadImg::class, $foundUploadImg);
        $this->assertSame('/path/to/image.jpg', $foundUploadImg->getFile());
        $this->assertSame('https://example.com/image.jpg', $foundUploadImg->getUrl());
        $this->assertNotNull($foundUploadImg->getMall());
        $this->assertSame($mall->getId(), $foundUploadImg->getMall()->getId());
    }

    public function testFindOneByFile(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('/unique/path/image.png');
        $uploadImg->setUrl('https://example.com/unique.png');

        $repository->save($uploadImg);

        $foundUploadImg = $repository->findOneBy(['file' => '/unique/path/image.png']);
        $this->assertNotNull($foundUploadImg);
        $this->assertInstanceOf(UploadImg::class, $foundUploadImg);
        $this->assertSame('/unique/path/image.png', $foundUploadImg->getFile());
        $this->assertSame('https://example.com/unique.png', $foundUploadImg->getUrl());
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $mall1 = new Mall();
        $mall1->setName('Mall 1');
        self::getEntityManager()->persist($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall 2');
        self::getEntityManager()->persist($mall2);

        $img1 = new UploadImg();
        $img1->setMall($mall1);
        $img1->setFile('/mall1/image1.jpg');

        $img2 = new UploadImg();
        $img2->setMall($mall1);
        $img2->setFile('/mall1/image2.jpg');

        $img3 = new UploadImg();
        $img3->setMall($mall2);
        $img3->setFile('/mall2/image1.jpg');

        $repository->save($img1);
        $repository->save($img2);
        $repository->save($img3);

        $mall1Images = $repository->findBy(['mall' => $mall1]);
        $this->assertCount(2, $mall1Images);

        $mall2Images = $repository->findBy(['mall' => $mall2]);
        $this->assertCount(1, $mall2Images);
    }

    public function testFindAllReturnsAllUploadImgs(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        // 清空现有数据
        $allUploadImgs = $repository->findAll();
        foreach ($allUploadImgs as $uploadImg) {
            $this->assertInstanceOf(UploadImg::class, $uploadImg);
            $repository->remove($uploadImg);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $img1 = new UploadImg();
        $img1->setMall($mall);
        $img1->setFile('/path/image1.jpg');

        $img2 = new UploadImg();
        $img2->setMall($mall);
        $img2->setFile('/path/image2.jpg');

        $repository->save($img1);
        $repository->save($img2);

        $uploadImgs = $repository->findAll();
        $this->assertCount(2, $uploadImgs);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        // 清理现有数据
        $allUploadImgs = $repository->findAll();
        foreach ($allUploadImgs as $uploadImg) {
            $this->assertInstanceOf(UploadImg::class, $uploadImg);
            $repository->remove($uploadImg);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        for ($i = 1; $i <= 5; ++$i) {
            $img = new UploadImg();
            $img->setMall($mall);
            $img->setFile("/path/image_{$i}.jpg");
            $img->setUrl("https://example.com/image_{$i}.jpg");
            $repository->save($img);
        }

        $uploadImgs = $repository->findBy([], ['file' => 'ASC'], 2, 1);
        $this->assertCount(2, $uploadImgs);
        $this->assertInstanceOf(UploadImg::class, $uploadImgs[0]);
        $this->assertInstanceOf(UploadImg::class, $uploadImgs[1]);
        $this->assertSame('/path/image_2.jpg', $uploadImgs[0]->getFile());
        $this->assertSame('/path/image_3.jpg', $uploadImgs[1]->getFile());
    }

    public function testFindByWithNullUrl(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('/path/no_url_image.jpg');
        $uploadImg->setUrl(null);

        $repository->save($uploadImg);

        $imagesWithNullUrl = $repository->findBy(['url' => null]);
        $this->assertNotEmpty($imagesWithNullUrl);

        $found = false;
        foreach ($imagesWithNullUrl as $img) {
            $this->assertInstanceOf(UploadImg::class, $img);
            if ('/path/no_url_image.jpg' === $img->getFile()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRemoveUploadImg(): void
    {
        $repository = self::getService(UploadImgRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('/path/to_be_removed.jpg');

        $repository->save($uploadImg);
        $id = $uploadImg->getId();

        $repository->remove($uploadImg);

        $foundUploadImg = $repository->find($id);
        $this->assertNull($foundUploadImg);
    }

    protected function createNewEntity(): UploadImg
    {
        $mall = new Mall();
        $mall->setName('Test Mall ' . uniqid());
        $mall->setDescription('Test mall for upload image');

        self::getEntityManager()->persist($mall);

        $uploadImg = new UploadImg();
        $uploadImg->setMall($mall);
        $uploadImg->setFile('/path/to/test_image_' . uniqid() . '.jpg');
        $uploadImg->setUrl(null);

        return $uploadImg;
    }

    protected function getRepository(): UploadImgRepository
    {
        return self::getService(UploadImgRepository::class);
    }
}
