<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\Video;
use PinduoduoApiBundle\Repository\VideoRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(VideoRepository::class)]
#[RunTestsInSeparateProcesses]
final class VideoRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(VideoRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allVideos = $repository->findAll();
        foreach ($allVideos as $video) {
            $this->assertInstanceOf(Video::class, $video);
            $repository->remove($video);
        }

        // 创建关联的 Mall
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for Video');
        $mall->setDescription('Test mall for video data fixtures');
        self::getEntityManager()->persist($mall);

        // 添加一个测试数据以满足 DataFixtures 检查
        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/test_video.mp4');
        $video->setStatus(1);

        $repository->save($video);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(VideoRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindVideo(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/video.mp4');
        $video->setStatus(1);

        $repository->save($video);

        $foundVideo = $repository->find($video->getId());
        $this->assertNotNull($foundVideo);
        $this->assertInstanceOf(Video::class, $foundVideo);
        $this->assertSame('https://example.com/video.mp4', $foundVideo->getUrl());
        $this->assertSame(1, $foundVideo->getStatus());
        $this->assertSame($mall->getId(), $foundVideo->getMall()?->getId());
    }

    public function testFindOneByUrl(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/unique-video.mp4');
        $video->setStatus(0);

        $repository->save($video);

        $foundVideo = $repository->findOneBy(['url' => 'https://example.com/unique-video.mp4']);
        $this->assertNotNull($foundVideo);
        $this->assertInstanceOf(Video::class, $foundVideo);
        $this->assertSame('https://example.com/unique-video.mp4', $foundVideo->getUrl());
        $this->assertSame(0, $foundVideo->getStatus());
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall1 = new Mall();
        $mall1->setName('Mall 1');
        self::getEntityManager()->persist($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall 2');
        self::getEntityManager()->persist($mall2);

        $video1 = new Video();
        $video1->setMall($mall1);
        $video1->setUrl('https://example.com/mall1-video1.mp4');

        $video2 = new Video();
        $video2->setMall($mall1);
        $video2->setUrl('https://example.com/mall1-video2.mp4');

        $video3 = new Video();
        $video3->setMall($mall2);
        $video3->setUrl('https://example.com/mall2-video1.mp4');

        $repository->save($video1);
        $repository->save($video2);
        $repository->save($video3);

        $mall1Videos = $repository->findBy(['mall' => $mall1]);
        $this->assertCount(2, $mall1Videos);

        $mall2Videos = $repository->findBy(['mall' => $mall2]);
        $this->assertCount(1, $mall2Videos);
    }

    public function testFindByStatus(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video1 = new Video();
        $video1->setMall($mall);
        $video1->setUrl('https://example.com/active-video.mp4');
        $video1->setStatus(1);

        $video2 = new Video();
        $video2->setMall($mall);
        $video2->setUrl('https://example.com/inactive-video.mp4');
        $video2->setStatus(0);

        $repository->save($video1);
        $repository->save($video2);

        $activeVideos = $repository->findBy(['status' => 1]);
        $this->assertNotEmpty($activeVideos);

        $inactiveVideos = $repository->findBy(['status' => 0]);
        $this->assertNotEmpty($inactiveVideos);
    }

    public function testFindAllReturnsAllVideos(): void
    {
        $repository = self::getService(VideoRepository::class);

        // 清空现有数据
        $allVideos = $repository->findAll();
        foreach ($allVideos as $video) {
            $this->assertInstanceOf(Video::class, $video);
            $repository->remove($video);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video1 = new Video();
        $video1->setMall($mall);
        $video1->setUrl('https://example.com/video1.mp4');

        $video2 = new Video();
        $video2->setMall($mall);
        $video2->setUrl('https://example.com/video2.mp4');

        $repository->save($video1);
        $repository->save($video2);

        $videos = $repository->findAll();
        $this->assertCount(2, $videos);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(VideoRepository::class);

        // 清理现有数据
        $allVideos = $repository->findAll();
        foreach ($allVideos as $video) {
            $this->assertInstanceOf(Video::class, $video);
            $repository->remove($video);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        for ($i = 1; $i <= 5; ++$i) {
            $video = new Video();
            $video->setMall($mall);
            $video->setUrl("https://example.com/video_{$i}.mp4");
            $video->setStatus($i % 2);
            $repository->save($video);
        }

        $videos = $repository->findBy([], ['url' => 'ASC'], 2, 1);
        $this->assertCount(2, $videos);
        $this->assertInstanceOf(Video::class, $videos[0]);
        $this->assertInstanceOf(Video::class, $videos[1]);
        $this->assertSame('https://example.com/video_2.mp4', $videos[0]->getUrl());
        $this->assertSame('https://example.com/video_3.mp4', $videos[1]->getUrl());
    }

    public function testFindByWithNullStatus(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/no-status-video.mp4');
        $video->setStatus(null);

        $repository->save($video);

        $videosWithNullStatus = $repository->findBy(['status' => null]);
        $this->assertNotEmpty($videosWithNullStatus);

        $found = false;
        foreach ($videosWithNullStatus as $vid) {
            $this->assertInstanceOf(Video::class, $vid);
            if ('https://example.com/no-status-video.mp4' === $vid->getUrl()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRemoveVideo(): void
    {
        $repository = self::getService(VideoRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/to-be-removed.mp4');

        $repository->save($video);
        $id = $video->getId();

        $repository->remove($video);

        $foundVideo = $repository->find($id);
        $this->assertNull($foundVideo);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(VideoRepository::class);

        $this->clearAllVideos($repository);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $video1 = new Video();
        $video1->setMall($mall);
        $video1->setUrl('https://example.com/video-c.mp4');
        $video1->setStatus(3);
        $this->persistAndFlush($video1);

        $video2 = new Video();
        $video2->setMall($mall);
        $video2->setUrl('https://example.com/video-a.mp4');
        $video2->setStatus(1);
        $this->persistAndFlush($video2);

        $video3 = new Video();
        $video3->setMall($mall);
        $video3->setUrl('https://example.com/video-b.mp4');
        $video3->setStatus(2);
        $this->persistAndFlush($video3);

        $firstVideoAsc = $repository->findOneBy([], ['url' => 'ASC']);
        $this->assertNotNull($firstVideoAsc);
        $this->assertInstanceOf(Video::class, $firstVideoAsc);
        $this->assertSame('https://example.com/video-a.mp4', $firstVideoAsc->getUrl());

        $firstVideoDesc = $repository->findOneBy([], ['url' => 'DESC']);
        $this->assertNotNull($firstVideoDesc);
        $this->assertInstanceOf(Video::class, $firstVideoDesc);
        $this->assertSame('https://example.com/video-c.mp4', $firstVideoDesc->getUrl());

        $lowestStatusVideo = $repository->findOneBy([], ['status' => 'ASC']);
        $this->assertNotNull($lowestStatusVideo);
        $this->assertInstanceOf(Video::class, $lowestStatusVideo);
        $this->assertSame(1, $lowestStatusVideo->getStatus());

        $highestStatusVideo = $repository->findOneBy([], ['status' => 'DESC']);
        $this->assertNotNull($highestStatusVideo);
        $this->assertInstanceOf(Video::class, $highestStatusVideo);
        $this->assertSame(3, $highestStatusVideo->getStatus());

        $newestVideo = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestVideo);
        $this->assertInstanceOf(Video::class, $newestVideo);
        $this->assertSame($video3->getId(), $newestVideo->getId());
    }

    private function clearAllVideos(VideoRepository $repository): void
    {
        $allVideos = $repository->findAll();
        foreach ($allVideos as $video) {
            self::getEntityManager()->remove($video);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Video
    {
        $mall = new Mall();
        $mall->setName('Test Mall ' . uniqid());
        $mall->setDescription('Test mall for video');

        self::getEntityManager()->persist($mall);

        $video = new Video();
        $video->setMall($mall);
        $video->setUrl('https://example.com/test_video_' . uniqid() . '.mp4');
        $video->setStatus(1);

        return $video;
    }

    protected function getRepository(): VideoRepository
    {
        return self::getService(VideoRepository::class);
    }
}
