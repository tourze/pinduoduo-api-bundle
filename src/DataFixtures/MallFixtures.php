<?php

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Mall;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class MallFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试用的 Mall 数据
        $mall = new Mall();
        $mall->setName('Test Mall for Development');
        $mall->setDescription('This is a test mall for development and testing purposes');

        $manager->persist($mall);
        $manager->flush();
    }
}
