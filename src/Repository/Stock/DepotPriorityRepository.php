<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DepotPriority>
 */
#[AsRepository(entityClass: DepotPriority::class)]
final class DepotPriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepotPriority::class);
    }

    /**
     * @return array<DepotPriority>
     */
    public function findByDepotId(string $depotId): array
    {
        return $this->findBy(['depotId' => $depotId], ['priority' => 'ASC']);
    }

    /**
     * @return array<DepotPriority>
     */
    public function findByRegion(int $provinceId, ?int $cityId = null, ?int $districtId = null): array
    {
        $qb = $this->createQueryBuilder('dp')
            ->andWhere('dp.provinceId = :provinceId')
            ->setParameter('provinceId', $provinceId)
        ;

        if (null !== $cityId) {
            $qb->andWhere('dp.cityId = :cityId')
                ->setParameter('cityId', $cityId)
            ;
        }

        if (null !== $districtId) {
            $qb->andWhere('dp.districtId = :districtId')
                ->setParameter('districtId', $districtId)
            ;
        }

        /** @var array<DepotPriority> */
        return $qb->orderBy('dp.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<DepotPriority>
     */
    public function findByPriorityType(DepotPriorityTypeEnum $priorityType): array
    {
        return $this->findBy(['priorityType' => $priorityType], ['priority' => 'ASC']);
    }

    /**
     * @return array<DepotPriority>
     */
    public function findActivePriorities(): array
    {
        return $this->findBy(['status' => DepotStatusEnum::ACTIVE], ['priority' => 'ASC']);
    }

    public function findByDepotAndRegion(string $depotId, int $provinceId, int $cityId, int $districtId): ?DepotPriority
    {
        $result = $this->findOneBy([
            'depotId' => $depotId,
            'provinceId' => $provinceId,
            'cityId' => $cityId,
            'districtId' => $districtId,
        ]);

        return $result instanceof DepotPriority ? $result : null;
    }

    /**
     * @return array<DepotPriority>
     */
    public function findByPriorityRange(int $minPriority, int $maxPriority): array
    {
        /** @var array<DepotPriority> */
        return $this->createQueryBuilder('dp')
            ->andWhere('dp.priority >= :minPriority')
            ->andWhere('dp.priority <= :maxPriority')
            ->setParameter('minPriority', $minPriority)
            ->setParameter('maxPriority', $maxPriority)
            ->orderBy('dp.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<DepotPriority>
     */
    public function findByDepotCode(string $depotCode): array
    {
        return $this->findBy(['depotCode' => $depotCode], ['priority' => 'ASC']);
    }

    public function save(DepotPriority $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DepotPriority $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
