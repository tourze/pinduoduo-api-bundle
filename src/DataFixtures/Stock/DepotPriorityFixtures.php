<?php

namespace PinduoduoApiBundle\DataFixtures\Stock;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class DepotPriorityFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly DepotRepository $depotRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取已存在的仓库
        $depot = $this->depotRepository->findOneBy([]);
        if (null === $depot) {
            return; // 如果没有仓库数据，跳过
        }

        // 创建仓库优先级数据
        $priority1 = new DepotPriority();
        $priority1->setDepot($depot);
        $priority1->setDepotCode($depot->getDepotCode());
        $priority1->setDepotId($depot->getDepotId());
        $priority1->setDepotName($depot->getDepotName());
        $priority1->setProvinceId(110000);
        $priority1->setCityId(110100);
        $priority1->setDistrictId(110101);
        $priority1->setPriority(1);
        $priority1->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $priority1->setStatus(DepotStatusEnum::ACTIVE);
        $manager->persist($priority1);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot);
        $priority2->setDepotCode($depot->getDepotCode());
        $priority2->setDepotId($depot->getDepotId());
        $priority2->setDepotName($depot->getDepotName());
        $priority2->setProvinceId(310000);
        $priority2->setCityId(310100);
        $priority2->setDistrictId(310101);
        $priority2->setPriority(2);
        $priority2->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $priority2->setStatus(DepotStatusEnum::ACTIVE);
        $manager->persist($priority2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DepotFixtures::class,
        ];
    }
}
