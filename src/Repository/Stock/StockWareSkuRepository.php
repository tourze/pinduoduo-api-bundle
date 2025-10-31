<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<StockWareSku>
 */
#[AsRepository(entityClass: StockWareSku::class)]
class StockWareSkuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWareSku::class);
    }

    /**
     * @return array<StockWareSku>
     */
    public function findByGoodsId(string $goodsId): array
    {
        return $this->findBy(['goodsId' => $goodsId]);
    }

    public function findBySkuId(string $skuId): ?StockWareSku
    {
        return $this->findOneBy(['skuId' => $skuId]);
    }

    public function findByGoodsIdAndSkuId(string $goodsId, string $skuId): ?StockWareSku
    {
        return $this->findOneBy([
            'goodsId' => $goodsId,
            'skuId' => $skuId,
        ]);
    }

    /**
     * @return array<StockWareSku>
     */
    public function findActiveSkus(): array
    {
        return $this->findBy(['status' => 1]);
    }

    /**
     * @return array<StockWareSku>
     */
    public function findByStockWareId(string $stockWareId): array
    {
        /** @var array<StockWareSku> */
        return $this->createQueryBuilder('sws')
            ->andWhere('sws.stockWare = :stockWareId')
            ->setParameter('stockWareId', $stockWareId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(StockWareSku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StockWareSku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
