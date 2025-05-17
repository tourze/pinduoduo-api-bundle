<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;

/**
 * @method StockWareDepot|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockWareDepot|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockWareDepot[] findAll()
 * @method StockWareDepot[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockWareDepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWareDepot::class);
    }

    public function findByStockWare(string $stockWareId): array
    {
        return $this->findBy(['stockWare' => $stockWareId]);
    }

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

    public function findByLocationCode(string $locationCode): array
    {
        return $this->findBy(['locationCode' => $locationCode]);
    }

    public function findLowStock(float $threshold): array
    {
        return $this->createQueryBuilder('swd')
            ->andWhere('swd.availableQuantity <= swd.warningThreshold')
            ->andWhere('swd.warningThreshold > :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();
    }

    public function findOverStock(): array
    {
        return $this->createQueryBuilder('swd')
            ->andWhere('swd.totalQuantity > swd.upperLimit')
            ->andWhere('swd.upperLimit > 0')
            ->getQuery()
            ->getResult();
    }
} 