<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class StockWareFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试货品数据
        $ware1 = new StockWare();
        $ware1->setWareSn('WARE001');
        $ware1->setWareName('测试货品1');
        $ware1->setType(StockWareTypeEnum::NORMAL);
        $ware1->setCreatedAt(time());
        $ware1->setUpdatedAt(time());
        $manager->persist($ware1);

        $ware2 = new StockWare();
        $ware2->setWareSn('WARE002');
        $ware2->setWareName('测试货品2');
        $ware2->setType(StockWareTypeEnum::NORMAL);
        $ware2->setCreatedAt(time());
        $ware2->setUpdatedAt(time());
        $manager->persist($ware2);

        $ware3 = new StockWare();
        $ware3->setWareSn('WARE003');
        $ware3->setWareName('测试货品3');
        $ware3->setType(StockWareTypeEnum::NORMAL);
        $ware3->setCreatedAt(time());
        $ware3->setUpdatedAt(time());
        $manager->persist($ware3);

        $manager->flush();
    }
}
