<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<StockWareSpec>
 */
#[AsRepository(entityClass: StockWareSpec::class)]
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

    /**
     * @return array<StockWareSpec>
     */
    public function findByStockWareSku(string $stockWareSkuId): array
    {
        return $this->findBy(['stockWareSku' => $stockWareSkuId]);
    }

    /**
     * @return array<StockWareSpec>
     */
    public function findBySpecKey(string $specKey): array
    {
        return $this->findBy(['specKey' => $specKey]);
    }

    public function save(StockWareSpec $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StockWareSpec $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
