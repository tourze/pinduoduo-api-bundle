<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\AuthCatRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '授权商家可发布的商品类目信息')]
#[ORM\Entity(repositoryClass: AuthCatRepository::class)]
#[ORM\Table(name: 'ims_pdd_auth_cat', options: ['comment' => '授权商家可发布的商品类目信息'])]
class AuthCat
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'authCats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '上级分类ID'])]
    private ?string $parentCatId = '0';

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '类目ID'])]
    private ?string $catId = null;

    #[ORM\Column(length: 120, options: ['comment' => '类目名称'])]
    private ?string $catName = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否为叶子类目'])]
    private ?bool $leaf = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): static
    {
        $this->mall = $mall;

        return $this;
    }

    public function getParentCatId(): ?string
    {
        return $this->parentCatId;
    }

    public function setParentCatId(?string $parentCatId): void
    {
        $this->parentCatId = $parentCatId;
    }

    public function getCatId(): ?string
    {
        return $this->catId;
    }

    public function setCatId(string $catId): static
    {
        $this->catId = $catId;

        return $this;
    }

    public function getCatName(): ?string
    {
        return $this->catName;
    }

    public function setCatName(string $catName): static
    {
        $this->catName = $catName;

        return $this;
    }

    public function isLeaf(): ?bool
    {
        return $this->leaf;
    }

    public function setLeaf(?bool $leaf): static
    {
        $this->leaf = $leaf;

        return $this;
    }
}
