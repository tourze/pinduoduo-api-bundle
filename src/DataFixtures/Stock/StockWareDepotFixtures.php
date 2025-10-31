<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class StockWareDepotFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Empty implementation - DataFixtures class exists to satisfy PHPStan rules
        $manager->flush();
    }
}
