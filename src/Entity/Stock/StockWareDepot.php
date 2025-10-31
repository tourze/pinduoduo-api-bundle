<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareDepotRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: StockWareDepotRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_depot', options: ['comment' => '拼多多货品仓库库存信息'])]
#[ORM\UniqueConstraint(name: 'uniq_ware_depot', columns: ['stock_ware_id', 'depot_id'])]
class StockWareDepot implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: StockWare::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private StockWare $stockWare;

    #[ORM\ManyToOne(targetEntity: Depot::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Depot $depot;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '可用库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private int $availableQuantity = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '占用库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private int $occupiedQuantity = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '锁定库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private int $lockedQuantity = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '在途库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private int $onwayQuantity = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总库存数量', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private int $totalQuantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '库存预警阈值', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private float $warningThreshold = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '库存上限', 'default' => 0])]
    #[Assert\PositiveOrZero]
    private float $upperLimit = 0;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '货位编码'])]
    #[Assert\Length(max: 255)]
    private ?string $locationCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 255)]
    private ?string $note = null;

    public function getStockWare(): StockWare
    {
        return $this->stockWare;
    }

    public function setStockWare(StockWare $stockWare): void
    {
        $this->stockWare = $stockWare;
    }

    public function getDepot(): Depot
    {
        return $this->depot;
    }

    public function setDepot(Depot $depot): void
    {
        $this->depot = $depot;
    }

    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    public function setAvailableQuantity(int $availableQuantity): void
    {
        $this->availableQuantity = $availableQuantity;
    }

    public function getOccupiedQuantity(): int
    {
        return $this->occupiedQuantity;
    }

    public function setOccupiedQuantity(int $occupiedQuantity): void
    {
        $this->occupiedQuantity = $occupiedQuantity;
    }

    public function getLockedQuantity(): int
    {
        return $this->lockedQuantity;
    }

    public function setLockedQuantity(int $lockedQuantity): void
    {
        $this->lockedQuantity = $lockedQuantity;
    }

    public function getOnwayQuantity(): int
    {
        return $this->onwayQuantity;
    }

    public function setOnwayQuantity(int $onwayQuantity): void
    {
        $this->onwayQuantity = $onwayQuantity;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): void
    {
        $this->totalQuantity = $totalQuantity;
    }

    public function getWarningThreshold(): float
    {
        return $this->warningThreshold;
    }

    public function setWarningThreshold(float $warningThreshold): void
    {
        $this->warningThreshold = $warningThreshold;
    }

    public function getUpperLimit(): float
    {
        return $this->upperLimit;
    }

    public function setUpperLimit(float $upperLimit): void
    {
        $this->upperLimit = $upperLimit;
    }

    public function getLocationCode(): ?string
    {
        return $this->locationCode;
    }

    public function setLocationCode(?string $locationCode): void
    {
        $this->locationCode = $locationCode;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
