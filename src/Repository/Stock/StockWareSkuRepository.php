<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;

/**
 * @method StockWareSku|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockWareSku|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockWareSku[] findAll()
 * @method StockWareSku[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockWareSkuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWareSku::class);
    }

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

    public function findActiveSkus(): array
    {
        return $this->findBy(['status' => 1]);
    }

    public function findByStockWareId(string $stockWareId): array
    {
        return $this->createQueryBuilder('sws')
            ->andWhere('sws.stockWare = :stockWareId')
            ->setParameter('stockWareId', $stockWareId)
            ->getQuery()
            ->getResult();
    }
} 