<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.add.depot
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.change.depot.info
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.depot.info.get
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.depot.list.get
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.mall.depot.simple.get
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.express.search.depot
 */
#[ORM\Entity(repositoryClass: DepotRepository::class)]
#[ORM\Table(name: 'pdd_depot', options: ['comment' => '拼多多仓库信息'])]
class Depot
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

    #[ORM\Column(type: 'bigint', nullable: true, options: ['comment' => '拼多多平台仓库ID'])]
    private ?string $depotId = null;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '仓库编码'])]
    private string $depotCode;

    #[ORM\Column(type: 'string', length: 100, options: ['comment' => '仓库名称'])]
    private string $depotName;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => '仓库别名'])]
    private string $depotAlias;

    #[ORM\Column(type: 'string', length: 20, options: ['comment' => '联系人'])]
    private string $contact;

    #[ORM\Column(type: 'string', length: 20, options: ['comment' => '联系电话'])]
    private string $phone;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '仓库地址'])]
    private string $address;

    #[ORM\Column(type: 'integer', options: ['comment' => '省份ID'])]
    private int $province;

    #[ORM\Column(type: 'integer', options: ['comment' => '城市ID'])]
    private int $city;

    #[ORM\Column(type: 'integer', options: ['comment' => '区县ID'])]
    private int $district;

    #[ORM\Column(type: 'string', length: 10, options: ['comment' => '邮编'])]
    private string $zipCode;

    #[ORM\Column(type: 'integer', enumType: DepotTypeEnum::class, options: ['comment' => '仓库类型'])]
    private DepotTypeEnum $type = DepotTypeEnum::SELF_BUILT;

    #[ORM\Column(type: 'integer', enumType: DepotBusinessTypeEnum::class, options: ['comment' => '仓库业务类型'])]
    private DepotBusinessTypeEnum $businessType = DepotBusinessTypeEnum::NORMAL;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '区域覆盖信息'])]
    private ?array $region = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '其他区域覆盖信息'])]
    private ?array $otherRegion = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '仓库面积(m²)', 'default' => 0])]
    private float $area = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '仓库容量(m³)', 'default' => 0])]
    private float $capacity = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '已使用容量(m³)', 'default' => 0])]
    private float $usedCapacity = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '货位数量', 'default' => 0])]
    private int $locationCount = 0;

    #[ORM\Column(type: 'integer', options: ['comment' => '已使用货位数量', 'default' => 0])]
    private int $usedLocationCount = 0;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为默认仓库', 'default' => false])]
    private bool $isDefault = false;

    #[ORM\Column(type: 'integer', enumType: DepotStatusEnum::class, options: ['comment' => '仓库状态'])]
    private DepotStatusEnum $status = DepotStatusEnum::ACTIVE;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    use TimestampableAware;

    public function getDepotId(): ?string
    {
        return $this->depotId;
    }

    public function setDepotId(?string $depotId): self
    {
        $this->depotId = $depotId;
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

    public function getDepotName(): string
    {
        return $this->depotName;
    }

    public function setDepotName(string $depotName): self
    {
        $this->depotName = $depotName;
        return $this;
    }

    public function getDepotAlias(): string
    {
        return $this->depotAlias;
    }

    public function setDepotAlias(string $depotAlias): self
    {
        $this->depotAlias = $depotAlias;
        return $this;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getProvince(): int
    {
        return $this->province;
    }

    public function setProvince(int $province): self
    {
        $this->province = $province;
        return $this;
    }

    public function getCity(): int
    {
        return $this->city;
    }

    public function setCity(int $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getDistrict(): int
    {
        return $this->district;
    }

    public function setDistrict(int $district): self
    {
        $this->district = $district;
        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getType(): DepotTypeEnum
    {
        return $this->type;
    }

    public function setType(DepotTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getBusinessType(): DepotBusinessTypeEnum
    {
        return $this->businessType;
    }

    public function setBusinessType(DepotBusinessTypeEnum $businessType): self
    {
        $this->businessType = $businessType;
        return $this;
    }

    public function getRegion(): ?array
    {
        return $this->region;
    }

    public function setRegion(?array $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getOtherRegion(): ?array
    {
        return $this->otherRegion;
    }

    public function setOtherRegion(?array $otherRegion): self
    {
        $this->otherRegion = $otherRegion;
        return $this;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function setArea(float $area): self
    {
        $this->area = $area;
        return $this;
    }

    public function getCapacity(): float
    {
        return $this->capacity;
    }

    public function setCapacity(float $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getUsedCapacity(): float
    {
        return $this->usedCapacity;
    }

    public function setUsedCapacity(float $usedCapacity): self
    {
        $this->usedCapacity = $usedCapacity;
        return $this;
    }

    public function getLocationCount(): int
    {
        return $this->locationCount;
    }

    public function setLocationCount(int $locationCount): self
    {
        $this->locationCount = $locationCount;
        return $this;
    }

    public function getUsedLocationCount(): int
    {
        return $this->usedLocationCount;
    }

    public function setUsedLocationCount(int $usedLocationCount): self
    {
        $this->usedLocationCount = $usedLocationCount;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
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