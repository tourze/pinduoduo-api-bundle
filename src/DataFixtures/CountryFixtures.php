<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Country;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $country1 = new Country();
        $country1->setName('中国');
        $manager->persist($country1);

        $country2 = new Country();
        $country2->setName('美国');
        $manager->persist($country2);

        $country3 = new Country();
        $country3->setName('日本');
        $manager->persist($country3);

        $manager->flush();
    }
}
