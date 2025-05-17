<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;

/**
 * @method DepotPriority|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepotPriority|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepotPriority[] findAll()
 * @method DepotPriority[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotPriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepotPriority::class);
    }

    public function findByDepotId(string $depotId): array
    {
        return $this->findBy(['depotId' => $depotId], ['priority' => 'ASC']);
    }

    public function findByRegion(int $provinceId, ?int $cityId = null, ?int $districtId = null): array
    {
        $qb = $this->createQueryBuilder('dp')
            ->andWhere('dp.provinceId = :provinceId')
            ->setParameter('provinceId', $provinceId);

        if ($cityId !== null) {
            $qb->andWhere('dp.cityId = :cityId')
                ->setParameter('cityId', $cityId);
        }

        if ($districtId !== null) {
            $qb->andWhere('dp.districtId = :districtId')
                ->setParameter('districtId', $districtId);
        }

        return $qb->orderBy('dp.priority', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPriorityType(DepotPriorityTypeEnum $priorityType): array
    {
        return $this->findBy(['priorityType' => $priorityType], ['priority' => 'ASC']);
    }

    public function findActivePriorities(): array
    {
        return $this->findBy(['status' => DepotStatusEnum::ACTIVE], ['priority' => 'ASC']);
    }

    public function findByDepotAndRegion(string $depotId, int $provinceId, int $cityId, int $districtId): ?DepotPriority
    {
        return $this->findOneBy([
            'depotId' => $depotId,
            'provinceId' => $provinceId,
            'cityId' => $cityId,
            'districtId' => $districtId,
        ]);
    }

    public function findByPriorityRange(int $minPriority, int $maxPriority): array
    {
        return $this->createQueryBuilder('dp')
            ->andWhere('dp.priority >= :minPriority')
            ->andWhere('dp.priority <= :maxPriority')
            ->setParameter('minPriority', $minPriority)
            ->setParameter('maxPriority', $maxPriority)
            ->orderBy('dp.priority', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDepotCode(string $depotCode): array
    {
        return $this->findBy(['depotCode' => $depotCode], ['priority' => 'ASC']);
    }
} 