<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Repository\Stock\DepotPriorityRepository;
use Symfony\Component\Validator\Constraints as Assert;
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

    #[ORM\ManyToOne(targetEntity: Depot::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Depot $depot;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '仓库编码'])]
    private string $depotCode;

    #[Assert\Length(max: 255)]
    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '拼多多平台仓库ID'])]
    private ?string $depotId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '仓库名称'])]
    private string $depotName;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '省份ID'])]
    private int $provinceId;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '城市ID'])]
    private int $cityId;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '区县ID'])]
    private int $districtId;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '优先级，数字越小优先级越高', 'default' => 0])]
    private int $priority = 0;

    #[Assert\Choice(callback: [DepotPriorityTypeEnum::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotPriorityTypeEnum::class, options: ['comment' => '优先级类型'])]
    private DepotPriorityTypeEnum $priorityType = DepotPriorityTypeEnum::NORMAL;

    #[Assert\Choice(callback: [DepotStatusEnum::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotStatusEnum::class, options: ['comment' => '状态'])]
    private DepotStatusEnum $status = DepotStatusEnum::ACTIVE;

    public function getDepot(): Depot
    {
        return $this->depot;
    }

    public function setDepot(Depot $depot): void
    {
        $this->depot = $depot;
    }

    public function getDepotCode(): string
    {
        return $this->depotCode;
    }

    public function setDepotCode(string $depotCode): void
    {
        $this->depotCode = $depotCode;
    }

    public function getDepotId(): ?string
    {
        return $this->depotId;
    }

    public function setDepotId(?string $depotId): void
    {
        $this->depotId = $depotId;
    }

    public function getDepotName(): string
    {
        return $this->depotName;
    }

    public function setDepotName(string $depotName): void
    {
        $this->depotName = $depotName;
    }

    public function getProvinceId(): int
    {
        return $this->provinceId;
    }

    public function setProvinceId(int $provinceId): void
    {
        $this->provinceId = $provinceId;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function setCityId(int $cityId): void
    {
        $this->cityId = $cityId;
    }

    public function getDistrictId(): int
    {
        return $this->districtId;
    }

    public function setDistrictId(int $districtId): void
    {
        $this->districtId = $districtId;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getPriorityType(): DepotPriorityTypeEnum
    {
        return $this->priorityType;
    }

    public function setPriorityType(DepotPriorityTypeEnum $priorityType): void
    {
        $this->priorityType = $priorityType;
    }

    public function getStatus(): DepotStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DepotStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
