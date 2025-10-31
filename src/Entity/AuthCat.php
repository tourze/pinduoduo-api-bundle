<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\AuthCatRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AuthCatRepository::class)]
#[ORM\Table(name: 'ims_pdd_auth_cat', options: ['comment' => '授权商家可发布的商品类目信息'])]
class AuthCat implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'authCats', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '上级分类ID'])]
    private ?string $parentCatId = '0';

    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '类目ID'])]
    private ?string $catId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, options: ['comment' => '类目名称'])]
    private ?string $catName = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否为叶子类目'])]
    private ?bool $leaf = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
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

    public function setCatId(string $catId): void
    {
        $this->catId = $catId;
    }

    public function getCatName(): ?string
    {
        return $this->catName;
    }

    public function setCatName(string $catName): void
    {
        $this->catName = $catName;
    }

    public function isLeaf(): ?bool
    {
        return $this->leaf;
    }

    public function setLeaf(?bool $leaf): void
    {
        $this->leaf = $leaf;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
