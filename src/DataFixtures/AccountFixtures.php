<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Enum\ApplicationType;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $account1 = new Account();
        $account1->setTitle('测试应用1');
        $account1->setClientId('test_client_id_1');
        $account1->setClientSecret('test_client_secret_1');
        $account1->setApplicationType(ApplicationType::企业ERP);
        $manager->persist($account1);

        $account2 = new Account();
        $account2->setTitle('测试应用2');
        $account2->setClientId('test_client_id_2');
        $account2->setClientSecret('test_client_secret_2');
        $account2->setApplicationType(ApplicationType::订单处理);
        $manager->persist($account2);

        $manager->flush();
    }
}
