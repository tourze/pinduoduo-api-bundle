<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\DataFixtures\Goods;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Goods\Category;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建顶级分类
        $category1 = new Category();
        $category1->setName('电子产品');
        $category1->setLevel(1);
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('服装鞋帽');
        $category2->setLevel(1);
        $manager->persist($category2);

        // 创建二级分类
        $category3 = new Category();
        $category3->setName('手机数码');
        $category3->setLevel(2);
        $category3->setParent($category1);
        $manager->persist($category3);

        $manager->flush();
    }
}
