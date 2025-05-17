<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\StockWare;

/**
 * @method StockWare|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockWare|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockWare[] findAll()
 * @method StockWare[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockWareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockWare::class);
    }

    public function findByWareCode(string $wareCode): ?StockWare
    {
        return $this->findOneBy(['wareCode' => $wareCode]);
    }

    public function findByWareName(string $wareName): array
    {
        return $this->findBy(['wareName' => $wareName]);
    }

    public function findByBarCode(string $barCode): ?StockWare
    {
        return $this->findOneBy(['barCode' => $barCode]);
    }

    public function findActiveWares(): array
    {
        return $this->findBy(['status' => 1]);
    }

    public function findByBrand(string $brand): array
    {
        return $this->findBy(['brand' => $brand]);
    }
} 