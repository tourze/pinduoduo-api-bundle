<?php

namespace PinduoduoApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\Mall;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @method Mall|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mall|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mall[] findAll()
 * @method Mall[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[Autoconfigure(public: true)]
class MallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mall::class);
    }
}
