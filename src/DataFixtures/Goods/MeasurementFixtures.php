<?php

namespace PinduoduoApiBundle\DataFixtures\Goods;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class MeasurementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试用的计量单位数据
        $measurement = new Measurement();
        $measurement->setCode('件');
        $measurement->setDescription('按件计量');

        $manager->persist($measurement);
        $manager->flush();
    }
}
