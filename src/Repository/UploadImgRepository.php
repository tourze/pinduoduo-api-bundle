<?php

namespace PinduoduoApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PinduoduoApiBundle\Entity\UploadImg;

/**
 * @method UploadImg|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadImg|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadImg[] findAll()
 * @method UploadImg[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadImgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadImg::class);
    }
}
