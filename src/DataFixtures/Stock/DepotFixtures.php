<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class DepotFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试仓库数据
        $depot1 = new Depot();
        $depot1->setDepotCode('DEPOT001');
        $depot1->setDepotName('测试仓库1');
        $depot1->setDepotAlias('仓库1');
        $depot1->setContact('张三');
        $depot1->setPhone('13800138001');
        $depot1->setAddress('北京市朝阳区测试路1号');
        $depot1->setProvince(1);
        $depot1->setCity(1);
        $depot1->setDistrict(1);
        $depot1->setZipCode('100000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot1->setStatus(DepotStatusEnum::ACTIVE);
        $depot1->setIsDefault(true);
        $depot1->setArea(1000.00);
        $depot1->setCapacity(5000.00);
        $depot1->setUsedCapacity(2000.00);
        $depot1->setLocationCount(100);
        $depot1->setUsedLocationCount(50);

        $manager->persist($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('DEPOT002');
        $depot2->setDepotName('测试仓库2');
        $depot2->setDepotAlias('仓库2');
        $depot2->setContact('李四');
        $depot2->setPhone('13800138002');
        $depot2->setAddress('上海市浦东新区测试路2号');
        $depot2->setProvince(2);
        $depot2->setCity(2);
        $depot2->setDistrict(2);
        $depot2->setZipCode('200000');
        $depot2->setType(DepotTypeEnum::THIRD_PARTY);
        $depot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot2->setStatus(DepotStatusEnum::ACTIVE);
        $depot2->setIsDefault(false);
        $depot2->setArea(800.00);
        $depot2->setCapacity(4000.00);
        $depot2->setUsedCapacity(1500.00);
        $depot2->setLocationCount(80);
        $depot2->setUsedLocationCount(40);

        $manager->persist($depot2);

        $depot3 = new Depot();
        $depot3->setDepotCode('DEPOT003');
        $depot3->setDepotName('测试仓库3');
        $depot3->setDepotAlias('仓库3');
        $depot3->setContact('王五');
        $depot3->setPhone('13800138003');
        $depot3->setAddress('广州市天河区测试路3号');
        $depot3->setProvince(3);
        $depot3->setCity(3);
        $depot3->setDistrict(3);
        $depot3->setZipCode('510000');
        $depot3->setType(DepotTypeEnum::SELF_BUILT);
        $depot3->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot3->setStatus(DepotStatusEnum::DISABLED);
        $depot3->setIsDefault(false);
        $depot3->setArea(600.00);
        $depot3->setCapacity(3000.00);
        $depot3->setUsedCapacity(0.00);
        $depot3->setLocationCount(60);
        $depot3->setUsedLocationCount(0);

        $manager->persist($depot3);

        $manager->flush();
    }
}
