<?php

namespace PinduoduoApiBundle\Repository\Stock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Depot>
 */
#[AsRepository(entityClass: Depot::class)]
final class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    public function findByDepotCode(string $depotCode): ?Depot
    {
        $result = $this->findOneBy(['depotCode' => $depotCode]);

        return $result instanceof Depot ? $result : null;
    }

    /**
     * @return array<Depot>
     */
    public function findByDepotName(string $depotName): array
    {
        return $this->findBy(['depotName' => $depotName]);
    }

    public function findDefaultDepot(): ?Depot
    {
        $result = $this->findOneBy(['isDefault' => true]);

        return $result instanceof Depot ? $result : null;
    }

    /**
     * @return array<Depot>
     */
    public function findActiveDepots(): array
    {
        return $this->findBy(['status' => DepotStatusEnum::ACTIVE]);
    }

    public function findByPddDepotId(string $depotId): ?Depot
    {
        $result = $this->findOneBy(['depotId' => $depotId]);

        return $result instanceof Depot ? $result : null;
    }

    /**
     * @return array<Depot>
     */
    public function findByType(DepotTypeEnum $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    /**
     * @return array<Depot>
     */
    public function findByRegion(int $province, ?int $city = null, ?int $district = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.province = :province')
            ->setParameter('province', $province)
        ;

        if (null !== $city) {
            $qb->andWhere('d.city = :city')
                ->setParameter('city', $city)
            ;
        }

        if (null !== $district) {
            $qb->andWhere('d.district = :district')
                ->setParameter('district', $district)
            ;
        }

        /** @var array<Depot> */
        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<Depot>
     */
    public function findByRegionCoverage(int $province, int $city, int $district): array
    {
        /** @var array<Depot> */
        return $this->createQueryBuilder('d')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :province) IS NOT NULL')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :city) IS NOT NULL')
            ->andWhere('JSON_SEARCH(d.region, \'one\', :district) IS NOT NULL')
            ->setParameter('province', (string) $province)
            ->setParameter('city', (string) $city)
            ->setParameter('district', (string) $district)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Depot>
     */
    public function findByContact(string $contact): array
    {
        /** @var array<Depot> */
        return $this->createQueryBuilder('d')
            ->andWhere('d.contact LIKE :contact OR d.phone LIKE :contact')
            ->setParameter('contact', '%' . $contact . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Depot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Depot $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
