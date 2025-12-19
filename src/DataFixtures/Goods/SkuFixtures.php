<?php

namespace PinduoduoApiBundle\DataFixtures\Goods;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SkuFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly GoodsRepository $goodsRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已创建的商品
        $goods = $this->goodsRepository->findOneBy([]);
        if (null === $goods) {
            return;
        }

        // 创建测试用的 SKU 数据
        $sku = new Sku();
        $sku->setGoods($goods);
        $sku->setSpecName('默认规格');
        $sku->setQuantity(100);
        $sku->setPrice(1000);
        $sku->setMultiPrice(900);
        $sku->setOnsale(true);

        $manager->persist($sku);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GoodsFixtures::class,
        ];
    }
}
