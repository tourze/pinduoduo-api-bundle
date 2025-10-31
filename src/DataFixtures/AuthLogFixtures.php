<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AccountRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AuthLogFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly MallRepository $mallRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $account = $this->accountRepository->findOneBy([]);
        $mall = $this->mallRepository->findOneBy([]);

        if (null !== $account && null !== $mall) {
            $authLog = new AuthLog();
            $authLog->setAccount($account);
            $authLog->setMall($mall);
            $authLog->setAccessToken('test_access_token_12345');
            $authLog->setRefreshToken('test_refresh_token_12345');
            $authLog->setTokenExpireTime(new \DateTimeImmutable('+7 days'));

            $manager->persist($authLog);
            $manager->flush();
        }
    }

    /**
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            MallFixtures::class,
        ];
    }
}
