<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareDepotRepository;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

#[ORM\Entity(repositoryClass: StockWareDepotRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_depot', options: ['comment' => '拼多多货品仓库库存信息'])]
#[ORM\UniqueConstraint(name: 'uniq_ware_depot', columns: ['stock_ware_id', 'depot_id'])]
class StockWareDepot implements \Stringable
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

    #[ORM\ManyToOne(targetEntity: StockWare::class)]
    #[ORM\JoinColumn(nullable: false)]
    private StockWare $stockWare;

    #[ORM\ManyToOne(targetEntity: Depot::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Depot $depot;

    #[ORM\Column(type: 'integer', options: ['comment' => '可用库存数量', 'default' => 0])]
    private int $availableQuantity = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '占用库存数量', 'default' => 0])]
    private int $occupiedQuantity = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '锁定库存数量', 'default' => 0])]
    private int $lockedQuantity = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '在途库存数量', 'default' => 0])]
    private int $onwayQuantity = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '总库存数量', 'default' => 0])]
    private int $totalQuantity = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '库存预警阈值', 'default' => 0])]
    private float $warningThreshold = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '库存上限', 'default' => 0])]
    private float $upperLimit = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '货位编码'])]
    private ?string $locationCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $note = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    use TimestampableAware;

    public function getStockWare(): StockWare
    {
        return $this->stockWare;
    }

    public function setStockWare(StockWare $stockWare): self
    {
        $this->stockWare = $stockWare;
        return $this;
    }

    public function getDepot(): Depot
    {
        return $this->depot;
    }

    public function setDepot(Depot $depot): self
    {
        $this->depot = $depot;
        return $this;
    }

    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    public function setAvailableQuantity(int $availableQuantity): self
    {
        $this->availableQuantity = $availableQuantity;
        return $this;
    }

    public function getOccupiedQuantity(): int
    {
        return $this->occupiedQuantity;
    }

    public function setOccupiedQuantity(int $occupiedQuantity): self
    {
        $this->occupiedQuantity = $occupiedQuantity;
        return $this;
    }

    public function getLockedQuantity(): int
    {
        return $this->lockedQuantity;
    }

    public function setLockedQuantity(int $lockedQuantity): self
    {
        $this->lockedQuantity = $lockedQuantity;
        return $this;
    }

    public function getOnwayQuantity(): int
    {
        return $this->onwayQuantity;
    }

    public function setOnwayQuantity(int $onwayQuantity): self
    {
        $this->onwayQuantity = $onwayQuantity;
        return $this;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;
        return $this;
    }

    public function getWarningThreshold(): float
    {
        return $this->warningThreshold;
    }

    public function setWarningThreshold(float $warningThreshold): self
    {
        $this->warningThreshold = $warningThreshold;
        return $this;
    }

    public function getUpperLimit(): float
    {
        return $this->upperLimit;
    }

    public function setUpperLimit(float $upperLimit): self
    {
        $this->upperLimit = $upperLimit;
        return $this;
    }

    public function getLocationCode(): ?string
    {
        return $this->locationCode;
    }

    public function setLocationCode(?string $locationCode): self
    {
        $this->locationCode = $locationCode;
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