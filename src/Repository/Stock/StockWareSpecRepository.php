<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;

/**
 * @method StockWareSpec|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockWareSpec|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockWareSpec[] findAll()
 * @method StockWareSpec[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockWareSpecRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWareSpec::class);
    }

    public function findBySpecId(string $specId): ?StockWareSpec
    {
        return $this->findOneBy(['specId' => $specId]);
    }

    public function findByStockWareSku(string $stockWareSkuId): array
    {
        return $this->findBy(['stockWareSku' => $stockWareSkuId]);
    }

    public function findBySpecKey(string $specKey): array
    {
        return $this->findBy(['specKey' => $specKey]);
    }
} 