<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: StockWareSkuRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_sku', options: ['comment' => '拼多多货品SKU关联信息'])]
class StockWareSku implements \Stringable
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

    #[ORM\ManyToOne(targetEntity: StockWare::class, inversedBy: 'stockWareSkus')]
    #[ORM\JoinColumn(nullable: false)]
    private StockWare $stockWare;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '商品ID'])]
    private string $goodsId;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => 'SKU ID'])]
    private string $skuId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => 'SKU名称'])]
    private string $skuName;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '库存数量', 'default' => 0])]
    private int $quantity = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否存在货品', 'default' => false])]
    private bool $existWare = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否在售', 'default' => false])]
    private bool $isOnsale = false;

    #[ORM\OneToMany(mappedBy: 'stockWareSku', targetEntity: StockWareSpec::class, cascade: ['persist', 'remove'])]
    private Collection $specs;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '关联状态：1-正常，2-停用'])]
    private int $status = 1;

    use TimestampableAware;
    use BlameableAware;

    public function __construct()
    {
        $this->specs = new ArrayCollection();
    }

    public function getStockWare(): StockWare
    {
        return $this->stockWare;
    }

    public function setStockWare(?StockWare $stockWare): self
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


    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
} 