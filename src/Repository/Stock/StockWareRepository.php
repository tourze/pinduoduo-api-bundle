<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<StockWare>
 */
#[AsRepository(entityClass: StockWare::class)]
final class StockWareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWare::class);
    }

    public function findByWareCode(string $wareCode): ?StockWare
    {
        return $this->findOneBy(['wareCode' => $wareCode]);
    }

    /**
     * @return array<StockWare>
     */
    public function findByWareName(string $wareName): array
    {
        return $this->findBy(['wareName' => $wareName]);
    }

    /**
     * 根据条码查找货品（实体无此字段，保留方法签名但返回null）.
     */
    public function findByBarCode(string $barCode): ?StockWare
    {
        // 实体中没有barCode字段，返回null
        return null;
    }

    /**
     * 查找激活的货品（实体无status字段，返回全部）.
     *
     * @return array<StockWare>
     */
    public function findActiveWares(): array
    {
        // 实体中没有status字段，返回全部货品
        return $this->findAll();
    }

    /**
     * @return array<StockWare>
     */
    public function findByBrand(string $brand): array
    {
        return $this->findBy(['brand' => $brand]);
    }

    public function save(StockWare $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StockWare $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
