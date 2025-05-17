<?php

namespace PinduoduoApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\LogisticsTemplate;

/**
 * @method LogisticsTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogisticsTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogisticsTemplate[] findAll()
 * @method LogisticsTemplate[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogisticsTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogisticsTemplate::class);
    }
}
