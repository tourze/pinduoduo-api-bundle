<?php

namespace PinduoduoApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\AuthCat;

/**
 * @method AuthCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthCat[] findAll()
 * @method AuthCat[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthCatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthCat::class);
    }
}
