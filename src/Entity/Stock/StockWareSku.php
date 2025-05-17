<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: StockWareSkuRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_sku', options: ['comment' => '拼多多货品SKU关联信息'])]
class StockWareSku
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    #[ORM\ManyToOne(targetEntity: StockWare::class, inversedBy: 'stockWareSkus')]
    #[ORM\JoinColumn(nullable: false)]
    private StockWare $stockWare;

    #[ORM\Column(type: 'bigint', options: ['comment' => '商品ID'])]
    private string $goodsId;

    #[ORM\Column(type: 'bigint', options: ['comment' => 'SKU ID'])]
    private string $skuId;

    #[ORM\Column(type: 'string', length: 100, options: ['comment' => 'SKU名称'])]
    private string $skuName;

    #[ORM\Column(type: 'integer', options: ['comment' => '库存数量', 'default' => 0])]
    private int $quantity = 0;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否存在货品', 'default' => false])]
    private bool $existWare = false;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否在售', 'default' => false])]
    private bool $isOnsale = false;

    #[ORM\OneToMany(mappedBy: 'stockWareSku', targetEntity: StockWareSpec::class, cascade: ['persist', 'remove'])]
    private Collection $specs;

    #[ORM\Column(type: 'integer', options: ['comment' => '关联状态：1-正常，2-停用'])]
    private int $status = 1;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

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

    public function __construct()
    {
        $this->specs = new ArrayCollection();
    }

    public function getStockWare(): StockWare
    {
        return $this->stockWare;
    }

    public function setStockWare(StockWare $stockWare): self
    {
        $this->stockWare = $stockWare;
        return $this;
    }

    public function getGoodsId(): string
    {
        return $this->goodsId;
    }

    public function setGoodsId(string $goodsId): self
    {
        $this->goodsId = $goodsId;
        return $this;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): self
    {
        $this->skuId = $skuId;
        return $this;
    }

    public function getSkuName(): string
    {
        return $this->skuName;
    }

    public function setSkuName(string $skuName): self
    {
        $this->skuName = $skuName;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function isExistWare(): bool
    {
        return $this->existWare;
    }

    public function setExistWare(bool $existWare): self
    {
        $this->existWare = $existWare;
        return $this;
    }

    public function isOnsale(): bool
    {
        return $this->isOnsale;
    }

    public function setIsOnsale(bool $isOnsale): self
    {
        $this->isOnsale = $isOnsale;
        return $this;
    }

    public function getSpecs(): Collection
    {
        return $this->specs;
    }

    public function addSpec(StockWareSpec $spec): self
    {
        if (!$this->specs->contains($spec)) {
            $this->specs->add($spec);
            $spec->setStockWareSku($this);
        }
        return $this;
    }

    public function removeSpec(StockWareSpec $spec): self
    {
        if ($this->specs->removeElement($spec)) {
            if ($spec->getStockWareSku() === $this) {
                $spec->setStockWareSku(null);
            }
        }
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
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
} 