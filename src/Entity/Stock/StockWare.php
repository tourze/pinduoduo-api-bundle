<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.ware.create
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.ware.delete
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.ware.detail.query
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.ware.info.list
 */
#[ORM\Entity(repositoryClass: StockWareRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware', options: ['comment' => '拼多多货品信息'])]
class StockWare implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '拼多多平台货品ID'])]
    private ?string $wareId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '货品编码'])]
    private string $wareSn;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '货品名称'])]
    private string $wareName;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '规格'])]
    private ?string $specification = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '单位'])]
    private ?string $unit = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '品牌'])]
    private ?string $brand = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '颜色'])]
    private ?string $color = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '包装'])]
    private ?string $packing = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $note = null;

    #[ORM\Column(type: Types::INTEGER, enumType: StockWareTypeEnum::class, options: ['comment' => '货品类型'])]
    private StockWareTypeEnum $type = StockWareTypeEnum::NORMAL;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '毛重(kg)', 'default' => 0])]
    private float $grossWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '净重(kg)', 'default' => 0])]
    private float $netWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '皮重(kg)', 'default' => 0])]
    private float $tareWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '重量(kg)', 'default' => 0])]
    private float $weight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '长度(cm)', 'default' => 0])]
    private float $length = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '宽度(cm)', 'default' => 0])]
    private float $width = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '高度(cm)', 'default' => 0])]
    private float $height = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '体积(m³)', 'default' => 0])]
    private float $volume = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '价格(元)', 'default' => 0])]
    private float $price = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '服务质量', 'default' => 0])]
    private int $serviceQuality = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '库存数量', 'default' => 0])]
    private int $quantity = 0;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '货品信息'])]
    private ?array $wareInfos = null;

    #[ORM\OneToMany(mappedBy: 'stockWare', targetEntity: StockWareSku::class, cascade: ['persist', 'remove'])]
    private Collection $stockWareSkus;

    #[ORM\OneToMany(mappedBy: 'stockWare', targetEntity: StockWareDepot::class, cascade: ['persist', 'remove'])]
    private Collection $stockWareDepots;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '创建时间戳'])]
    private int $createdAt = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '更新时间戳'])]
    private int $updatedAt = 0;

    public function __construct()
    {
        $this->stockWareSkus = new ArrayCollection();
        $this->stockWareDepots = new ArrayCollection();
    }

    public function getWareId(): ?string
    {
        return $this->wareId;
    }

    public function setWareId(?string $wareId): self
    {
        $this->wareId = $wareId;
        return $this;
    }

    public function getWareSn(): string
    {
        return $this->wareSn;
    }

    public function setWareSn(string $wareSn): self
    {
        $this->wareSn = $wareSn;
        return $this;
    }

    public function getWareName(): string
    {
        return $this->wareName;
    }

    public function setWareName(string $wareName): self
    {
        $this->wareName = $wareName;
        return $this;
    }

    public function getSpecification(): ?string
    {
        return $this->specification;
    }

    public function setSpecification(?string $specification): self
    {
        $this->specification = $specification;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getPacking(): ?string
    {
        return $this->packing;
    }

    public function setPacking(?string $packing): self
    {
        $this->packing = $packing;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getType(): StockWareTypeEnum
    {
        return $this->type;
    }

    public function setType(StockWareTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getGrossWeight(): float
    {
        return $this->grossWeight;
    }

    public function setGrossWeight(float $grossWeight): self
    {
        $this->grossWeight = $grossWeight;
        return $this;
    }

    public function getNetWeight(): float
    {
        return $this->netWeight;
    }

    public function setNetWeight(float $netWeight): self
    {
        $this->netWeight = $netWeight;
        return $this;
    }

    public function getTareWeight(): float
    {
        return $this->tareWeight;
    }

    public function setTareWeight(float $tareWeight): self
    {
        $this->tareWeight = $tareWeight;
        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getServiceQuality(): int
    {
        return $this->serviceQuality;
    }

    public function setServiceQuality(int $serviceQuality): self
    {
        $this->serviceQuality = $serviceQuality;
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

    public function getWareInfos(): ?array
    {
        return $this->wareInfos;
    }

    public function setWareInfos(?array $wareInfos): self
    {
        $this->wareInfos = $wareInfos;
        return $this;
    }

    public function getStockWareSkus(): Collection
    {
        return $this->stockWareSkus;
    }

    public function addStockWareSku(StockWareSku $stockWareSku): self
    {
        if (!$this->stockWareSkus->contains($stockWareSku)) {
            $this->stockWareSkus->add($stockWareSku);
            $stockWareSku->setStockWare($this);
        }
        return $this;
    }

    public function removeStockWareSku(StockWareSku $stockWareSku): self
    {
        if ($this->stockWareSkus->removeElement($stockWareSku)) {
            if ($stockWareSku->getStockWare() === $this) {
                $stockWareSku->setStockWare(null);
            }
        }
        return $this;
    }

    public function getStockWareDepots(): Collection
    {
        return $this->stockWareDepots;
    }

    public function addStockWareDepot(StockWareDepot $stockWareDepot): self
    {
        if (!$this->stockWareDepots->contains($stockWareDepot)) {
            $this->stockWareDepots->add($stockWareDepot);
            $stockWareDepot->setStockWare($this);
        }
        return $this;
    }

    public function removeStockWareDepot(StockWareDepot $stockWareDepot): self
    {
        if ($this->stockWareDepots->removeElement($stockWareDepot)) {
            if ($stockWareDepot->getStockWare() === $this) {
                $stockWareDepot->setStockWare(null);
            }
        }
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
} 