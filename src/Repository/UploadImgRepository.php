<?php

namespace PinduoduoApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use FileSystemBundle\Service\TemporaryFileService;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Service\SdkService;

/**
 * @method UploadImg|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadImg|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadImg[] findAll()
 * @method UploadImg[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadImgRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly SdkService $sdkService,
        private readonly TemporaryFileService $temporaryFileService,
    ) {
        parent::__construct($registry, UploadImg::class);
    }
}
