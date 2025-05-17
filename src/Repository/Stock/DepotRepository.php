<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;

/**
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[] findAll()
 * @method Depot[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    public function findByDepotCode(string $depotCode): ?Depot
    {
        return $this->findOneBy(['depotCode' => $depotCode]);
    }

    public function findByDepotName(string $depotName): array
    {
        return $this->findBy(['depotName' => $depotName]);
    }

    public function findDefaultDepot(): ?Depot
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    public function findActiveDepots(): array
    {
        return $this->findBy(['status' => DepotStatusEnum::ACTIVE]);
    }

    public function findByPddDepotId(string $depotId): ?Depot
    {
        return $this->findOneBy(['depotId' => $depotId]);
    }

    public function findByType(DepotTypeEnum $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findByRegion(int $province, ?int $city = null, ?int $district = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.province = :province')
            ->setParameter('province', $province);

        if ($city !== null) {
            $qb->andWhere('d.city = :city')
                ->setParameter('city', $city);
        }

        if ($district !== null) {
            $qb->andWhere('d.district = :district')
                ->setParameter('district', $district);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByRegionCoverage(int $province, int $city, int $district): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :province) IS NOT NULL')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :city) IS NOT NULL')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :district) IS NOT NULL')
            ->setParameter('province', (string)$province)
            ->setParameter('city', (string)$city)
            ->setParameter('district', (string)$district)
            ->getQuery()
            ->getResult();
    }

    public function findByContact(string $contact): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.contact LIKE :contact OR d.phone LIKE :contact')
            ->setParameter('contact', '%' . $contact . '%')
            ->getQuery()
            ->getResult();
    }
} 