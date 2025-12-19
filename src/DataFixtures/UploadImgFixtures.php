<?php

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class UploadImgFixtures extends Fixture implements DependentFixtureInterface
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

        // 创建测试图片数据
        $img1 = new UploadImg();
        $img1->setMall($mall);
        $img1->setFile('/test/images/product1.jpg');
        $img1->setUrl('https://images.unsplash.com/photo-1523275335684-37898b6baf30');
        $manager->persist($img1);

        $img2 = new UploadImg();
        $img2->setMall($mall);
        $img2->setFile('/test/images/product2.jpg');
        $img2->setUrl('https://images.unsplash.com/photo-1505740420928-5e560c06d30e');
        $manager->persist($img2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MallFixtures::class,
        ];
    }
}
