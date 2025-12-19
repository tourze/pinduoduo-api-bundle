<?php

namespace PinduoduoApiBundle\DataFixtures\Goods;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Goods\Spec;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SpecFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试用的规格数据
        $spec = new Spec();
        $spec->setName('颜色');

        $manager->persist($spec);
        $manager->flush();
    }
}
