<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSkuRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: StockWareSkuRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_sku', options: ['comment' => '拼多多货品SKU关联信息'])]
class StockWareSku implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: StockWare::class, inversedBy: 'stockWareSkus', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private StockWare $stockWare;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '商品ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $goodsId;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => 'SKU ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $skuId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => 'SKU名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $skuName;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 2147483647)]
    private int $quantity = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否存在货品', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $existWare = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否在售', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $isOnsale = false;

    /**
     * @var Collection<int, StockWareSpec>
     */
    #[ORM\OneToMany(mappedBy: 'stockWareSku', targetEntity: StockWareSpec::class, cascade: ['persist', 'remove'])]
    #[Assert\Type(type: 'object')]
    private Collection $specs;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '关联状态：1-正常，2-停用'])]
    #[Assert\Choice(choices: [1, 2])]
    private int $status = 1;

    public function __construct()
    {
        $this->specs = new ArrayCollection();
    }

    public function getStockWare(): StockWare
    {
        return $this->stockWare;
    }

    public function setStockWare(StockWare $stockWare): void
    {
        $this->stockWare = $stockWare;
    }

    public function getGoodsId(): string
    {
        return $this->goodsId;
    }

    public function setGoodsId(string $goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }

    public function getSkuName(): string
    {
        return $this->skuName;
    }

    public function setSkuName(string $skuName): void
    {
        $this->skuName = $skuName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function isExistWare(): bool
    {
        return $this->existWare;
    }

    public function setExistWare(bool $existWare): void
    {
        $this->existWare = $existWare;
    }

    public function isOnsale(): bool
    {
        return $this->isOnsale;
    }

    public function setIsOnsale(bool $isOnsale): void
    {
        $this->isOnsale = $isOnsale;
    }

    /**
     * @return Collection<int, StockWareSpec>
     */
    public function getSpecs(): Collection
    {
        return $this->specs;
    }

    public function addSpec(StockWareSpec $spec): void
    {
        if (!$this->specs->contains($spec)) {
            $this->specs->add($spec);
            $spec->setStockWareSku($this);
        }
    }

    public function removeSpec(StockWareSpec $spec): void
    {
        if ($this->specs->removeElement($spec)) {
            if ($spec->getStockWareSku() === $this) {
                $spec->setStockWareSku(null);
            }
        }
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
