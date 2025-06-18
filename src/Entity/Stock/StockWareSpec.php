<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSpecRepository;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

#[ORM\Entity(repositoryClass: StockWareSpecRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_spec', options: ['comment' => '拼多多货品规格信息'])]
class StockWareSpec implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    #[ORM\ManyToOne(targetEntity: StockWareSku::class, inversedBy: 'specs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?StockWareSku $stockWareSku = null;

    #[ORM\Column(type: 'bigint', options: ['comment' => '规格ID'])]
    private string $specId;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '规格名称'])]
    private string $specKey;

    #[ORM\Column(type: 'string', length: 100, options: ['comment' => '规格值'])]
    private string $specValue;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    use TimestampableAware;

    public function getStockWareSku(): ?StockWareSku
    {
        return $this->stockWareSku;
    }

    public function setStockWareSku(?StockWareSku $stockWareSku): self
    {
        $this->stockWareSku = $stockWareSku;
        return $this;
    }

    public function getSpecId(): string
    {
        return $this->specId;
    }

    public function setSpecId(string $specId): self
    {
        $this->specId = $specId;
        return $this;
    }

    public function getSpecKey(): string
    {
        return $this->specKey;
    }

    public function setSpecKey(string $specKey): self
    {
        $this->specKey = $specKey;
        return $this;
    }

    public function getSpecValue(): string
    {
        return $this->specValue;
    }

    public function setSpecValue(string $specValue): self
    {
        $this->specValue = $specValue;
        return $this;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
} 