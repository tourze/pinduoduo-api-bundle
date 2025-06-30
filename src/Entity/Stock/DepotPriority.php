<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Repository\Stock\DepotPriorityRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.depot.priority.list
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.stock.depot.priority.update
 */
#[ORM\Entity(repositoryClass: DepotPriorityRepository::class)]
#[ORM\Table(name: 'pdd_depot_priority', options: ['comment' => '拼多多仓库优先级信息'])]
#[ORM\UniqueConstraint(name: 'uniq_depot_region', columns: ['depot_id', 'province_id', 'city_id', 'district_id'])]
class DepotPriority implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: Depot::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Depot $depot;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '仓库编码'])]
    private string $depotCode;

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '拼多多平台仓库ID'])]
    private ?string $depotId = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '仓库名称'])]
    private string $depotName;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '省份ID'])]
    private int $provinceId;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '城市ID'])]
    private int $cityId;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '区县ID'])]
    private int $districtId;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '优先级，数字越小优先级越高', 'default' => 0])]
    private int $priority = 0;

    #[ORM\Column(type: Types::INTEGER, enumType: DepotPriorityTypeEnum::class, options: ['comment' => '优先级类型'])]
    private DepotPriorityTypeEnum $priorityType = DepotPriorityTypeEnum::NORMAL;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotStatusEnum::class, options: ['comment' => '状态'])]
    private DepotStatusEnum $status = DepotStatusEnum::ACTIVE;

    public function getDepot(): Depot
    {
        return $this->depot;
    }

    public function setDepot(Depot $depot): self
    {
        $this->depot = $depot;
        return $this;
    }

    public function getDepotCode(): string
    {
        return $this->depotCode;
    }

    public function setDepotCode(string $depotCode): self
    {
        $this->depotCode = $depotCode;
        return $this;
    }

    public function getDepotId(): ?string
    {
        return $this->depotId;
    }

    public function setDepotId(?string $depotId): self
    {
        $this->depotId = $depotId;
        return $this;
    }

    public function getDepotName(): string
    {
        return $this->depotName;
    }

    public function setDepotName(string $depotName): self
    {
        $this->depotName = $depotName;
        return $this;
    }

    public function getProvinceId(): int
    {
        return $this->provinceId;
    }

    public function setProvinceId(int $provinceId): self
    {
        $this->provinceId = $provinceId;
        return $this;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function setCityId(int $cityId): self
    {
        $this->cityId = $cityId;
        return $this;
    }

    public function getDistrictId(): int
    {
        return $this->districtId;
    }

    public function setDistrictId(int $districtId): self
    {
        $this->districtId = $districtId;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriorityType(): DepotPriorityTypeEnum
    {
        return $this->priorityType;
    }

    public function setPriorityType(DepotPriorityTypeEnum $priorityType): self
    {
        $this->priorityType = $priorityType;
        return $this;
    }

    public function getStatus(): DepotStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DepotStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }


    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
