<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AuthCatFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly MallRepository $mallRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $mall = $this->mallRepository->findOneBy([]);
        if (null === $mall) {
            // 如果没有 Mall，创建一个
            $mall = new Mall();
            $mall->setName('测试店铺');
            $mall->setDescription('用于测试的店铺');
            $manager->persist($mall);
            $manager->flush();
        }

        $authCat1 = new AuthCat();
        $authCat1->setMall($mall);
        $authCat1->setCatId('1001');
        $authCat1->setCatName('电子产品');
        $authCat1->setParentCatId('0');
        $authCat1->setLeaf(false);
        $manager->persist($authCat1);

        $authCat2 = new AuthCat();
        $authCat2->setMall($mall);
        $authCat2->setCatId('1002');
        $authCat2->setCatName('手机');
        $authCat2->setParentCatId('1001');
        $authCat2->setLeaf(true);
        $manager->persist($authCat2);

        $manager->flush();
    }

    /**
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            MallFixtures::class,
        ];
    }
}
