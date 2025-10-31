<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<StockWareDepot>
 */
#[AsRepository(entityClass: StockWareDepot::class)]
class StockWareDepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWareDepot::class);
    }

    /**
     * @return array<StockWareDepot>
     */
    public function findByStockWare(string $stockWareId): array
    {
        return $this->findBy(['stockWare' => $stockWareId]);
    }

    /**
     * @return array<StockWareDepot>
     */
    public function findByDepot(string $depotId): array
    {
        return $this->findBy(['depot' => $depotId]);
    }

    public function findByStockWareAndDepot(string $stockWareId, string $depotId): ?StockWareDepot
    {
        return $this->findOneBy([
            'stockWare' => $stockWareId,
            'depot' => $depotId,
        ]);
    }

    /**
     * @return array<StockWareDepot>
     */
    public function findByLocationCode(string $locationCode): array
    {
        return $this->findBy(['locationCode' => $locationCode]);
    }

    /**
     * @return array<StockWareDepot>
     */
    public function findLowStock(float $threshold): array
    {
        /** @var array<StockWareDepot> */
        return $this->createQueryBuilder('swd')
            ->andWhere('swd.availableQuantity <= swd.warningThreshold')
            ->andWhere('swd.warningThreshold > :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<StockWareDepot>
     */
    public function findOverStock(): array
    {
        /** @var array<StockWareDepot> */
        return $this->createQueryBuilder('swd')
            ->andWhere('swd.totalQuantity > swd.upperLimit')
            ->andWhere('swd.upperLimit > 0')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(StockWareDepot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StockWareDepot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
