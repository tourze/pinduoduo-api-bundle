<?php

namespace PinduoduoApiBundle\DataFixtures\Goods;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\Goods\GoodsStatus;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class GoodsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试店铺
        $mall = new Mall();
        $mall->setName('测试店铺');
        $manager->persist($mall);

        // 创建一个测试商品
        $goods = new Goods();
        $goods->setGoodsName('测试商品');
        $goods->setGoodsSn('TEST_SN_001');
        $goods->setStatus(GoodsStatus::Up);
        $goods->setGoodsType(GoodsType::国内普通商品);
        $goods->setMall($mall);
        $goods->setOnsale(true);
        $manager->persist($goods);

        $manager->flush();
    }
}
