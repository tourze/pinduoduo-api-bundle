<?php

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Video;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly MallRepository $mallRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的商城
        $mall = $this->mallRepository->findOneBy([]);
        if (null === $mall) {
            return; // 如果没有商城数据，跳过
        }

        // 创建测试视频数据
        $video1 = new Video();
        $video1->setMall($mall);
        $video1->setUrl('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d');
        $video1->setStatus(1);
        $manager->persist($video1);

        $video2 = new Video();
        $video2->setMall($mall);
        $video2->setUrl('https://images.unsplash.com/photo-1498050108023-c5249f4df085');
        $video2->setStatus(1);
        $manager->persist($video2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MallFixtures::class,
        ];
    }
}
