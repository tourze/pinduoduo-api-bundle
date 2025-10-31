<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use PinduoduoApiBundle\Repository\Stock\StockWareRepository;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[Assert\Length(max: 20)]
    private ?string $wareId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '货品编码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $wareSn;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '货品名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $wareName;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '规格'])]
    #[Assert\Length(max: 50)]
    private ?string $specification = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '单位'])]
    #[Assert\Length(max: 50)]
    private ?string $unit = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '品牌'])]
    #[Assert\Length(max: 50)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '颜色'])]
    #[Assert\Length(max: 50)]
    private ?string $color = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '包装'])]
    #[Assert\Length(max: 50)]
    private ?string $packing = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 255)]
    private ?string $note = null;

    #[ORM\Column(type: Types::INTEGER, enumType: StockWareTypeEnum::class, options: ['comment' => '货品类型'])]
    #[Assert\Choice(callback: [StockWareTypeEnum::class, 'cases'])]
    private StockWareTypeEnum $type = StockWareTypeEnum::NORMAL;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '毛重(kg)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $grossWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '净重(kg)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $netWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '皮重(kg)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $tareWeight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '重量(kg)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $weight = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '长度(cm)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $length = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '宽度(cm)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $width = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '高度(cm)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $height = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '体积(m³)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $volume = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '价格(元)', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999999.99)]
    private float $price = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '服务质量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 2147483647)]
    private int $serviceQuality = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 2147483647)]
    private int $quantity = 0;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '货品信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $wareInfos = null;

    /**
     * @var Collection<int, StockWareSku>
     */
    #[ORM\OneToMany(mappedBy: 'stockWare', targetEntity: StockWareSku::class, cascade: ['persist', 'remove'])]
    private Collection $stockWareSkus;

    /**
     * @var Collection<int, StockWareDepot>
     */
    #[ORM\OneToMany(mappedBy: 'stockWare', targetEntity: StockWareDepot::class, cascade: ['persist', 'remove'])]
    private Collection $stockWareDepots;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '创建时间戳'])]
    #[Assert\PositiveOrZero]
    private int $createdAt = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '更新时间戳'])]
    #[Assert\PositiveOrZero]
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

    public function setWareId(?string $wareId): void
    {
        $this->wareId = $wareId;
    }

    public function getWareSn(): string
    {
        return $this->wareSn;
    }

    public function setWareSn(string $wareSn): void
    {
        $this->wareSn = $wareSn;
    }

    public function getWareName(): string
    {
        return $this->wareName;
    }

    public function setWareName(string $wareName): void
    {
        $this->wareName = $wareName;
    }

    public function getSpecification(): ?string
    {
        return $this->specification;
    }

    public function setSpecification(?string $specification): void
    {
        $this->specification = $specification;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getPacking(): ?string
    {
        return $this->packing;
    }

    public function setPacking(?string $packing): void
    {
        $this->packing = $packing;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getType(): StockWareTypeEnum
    {
        return $this->type;
    }

    public function setType(StockWareTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getGrossWeight(): float
    {
        return $this->grossWeight;
    }

    public function setGrossWeight(float $grossWeight): void
    {
        $this->grossWeight = $grossWeight;
    }

    public function getNetWeight(): float
    {
        return $this->netWeight;
    }

    public function setNetWeight(float $netWeight): void
    {
        $this->netWeight = $netWeight;
    }

    public function getTareWeight(): float
    {
        return $this->tareWeight;
    }

    public function setTareWeight(float $tareWeight): void
    {
        $this->tareWeight = $tareWeight;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setLength(float $length): void
    {
        $this->length = $length;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): void
    {
        $this->volume = $volume;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getServiceQuality(): int
    {
        return $this->serviceQuality;
    }

    public function setServiceQuality(int $serviceQuality): void
    {
        $this->serviceQuality = $serviceQuality;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getWareInfos(): ?array
    {
        return $this->wareInfos;
    }

    /**
     * @param array<string, mixed>|null $wareInfos
     */
    public function setWareInfos(?array $wareInfos): void
    {
        $this->wareInfos = $wareInfos;
    }

    /**
     * @return Collection<int, StockWareSku>
     */
    public function getStockWareSkus(): Collection
    {
        return $this->stockWareSkus;
    }

    public function addStockWareSku(StockWareSku $stockWareSku): void
    {
        if (!$this->stockWareSkus->contains($stockWareSku)) {
            $this->stockWareSkus->add($stockWareSku);
            $stockWareSku->setStockWare($this);
        }
    }

    public function removeStockWareSku(StockWareSku $stockWareSku): void
    {
        $this->stockWareSkus->removeElement($stockWareSku);
    }

    /**
     * @return Collection<int, StockWareDepot>
     */
    public function getStockWareDepots(): Collection
    {
        return $this->stockWareDepots;
    }

    public function addStockWareDepot(StockWareDepot $stockWareDepot): void
    {
        if (!$this->stockWareDepots->contains($stockWareDepot)) {
            $this->stockWareDepots->add($stockWareDepot);
            $stockWareDepot->setStockWare($this);
        }
    }

    public function removeStockWareDepot(StockWareDepot $stockWareDepot): void
    {
        $this->stockWareDepots->removeElement($stockWareDepot);
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
