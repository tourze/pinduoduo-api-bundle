<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

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
class Depot implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '拼多多平台仓库ID'])]
    private ?string $depotId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '仓库编码'])]
    private string $depotCode;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '仓库名称'])]
    private string $depotName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '仓库别名'])]
    private string $depotAlias;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '联系人'])]
    private string $contact;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '联系电话'])]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '仓库地址'])]
    private string $address;

    #[Assert\Type(type: 'int')]
    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '省份ID'])]
    private int $province;

    #[Assert\Type(type: 'int')]
    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '城市ID'])]
    private int $city;

    #[Assert\Type(type: 'int')]
    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '区县ID'])]
    private int $district;

    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[ORM\Column(type: Types::STRING, length: 10, options: ['comment' => '邮编'])]
    private string $zipCode;

    #[Assert\Choice(callback: [DepotTypeEnum::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotTypeEnum::class, options: ['comment' => '仓库类型'])]
    private DepotTypeEnum $type = DepotTypeEnum::SELF_BUILT;

    #[Assert\Choice(callback: [DepotBusinessTypeEnum::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotBusinessTypeEnum::class, options: ['comment' => '仓库业务类型'])]
    private DepotBusinessTypeEnum $businessType = DepotBusinessTypeEnum::NORMAL;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '区域覆盖信息'])]
    private ?array $region = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '其他区域覆盖信息'])]
    private ?array $otherRegion = null;

    #[Assert\Type(type: 'float')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '仓库面积(m²)', 'default' => 0])]
    private float $area = 0;

    #[Assert\Type(type: 'float')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '仓库容量(m³)', 'default' => 0])]
    private float $capacity = 0;

    #[Assert\Type(type: 'float')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '已使用容量(m³)', 'default' => 0])]
    private float $usedCapacity = 0;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '货位数量', 'default' => 0])]
    private int $locationCount = 0;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '已使用货位数量', 'default' => 0])]
    private int $usedLocationCount = 0;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为默认仓库', 'default' => false])]
    private bool $isDefault = false;

    #[Assert\Choice(callback: [DepotStatusEnum::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: DepotStatusEnum::class, options: ['comment' => '仓库状态'])]
    private DepotStatusEnum $status = DepotStatusEnum::ACTIVE;

    public function getDepotId(): ?string
    {
        return $this->depotId;
    }

    public function setDepotId(?string $depotId): void
    {
        $this->depotId = $depotId;
    }

    public function getDepotCode(): string
    {
        return $this->depotCode;
    }

    public function setDepotCode(string $depotCode): void
    {
        $this->depotCode = $depotCode;
    }

    public function getDepotName(): string
    {
        return $this->depotName;
    }

    public function setDepotName(string $depotName): void
    {
        $this->depotName = $depotName;
    }

    public function getDepotAlias(): string
    {
        return $this->depotAlias;
    }

    public function setDepotAlias(string $depotAlias): void
    {
        $this->depotAlias = $depotAlias;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function setContact(string $contact): void
    {
        $this->contact = $contact;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getProvince(): int
    {
        return $this->province;
    }

    public function setProvince(int $province): void
    {
        $this->province = $province;
    }

    public function getCity(): int
    {
        return $this->city;
    }

    public function setCity(int $city): void
    {
        $this->city = $city;
    }

    public function getDistrict(): int
    {
        return $this->district;
    }

    public function setDistrict(int $district): void
    {
        $this->district = $district;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getType(): DepotTypeEnum
    {
        return $this->type;
    }

    public function setType(DepotTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getBusinessType(): DepotBusinessTypeEnum
    {
        return $this->businessType;
    }

    public function setBusinessType(DepotBusinessTypeEnum $businessType): void
    {
        $this->businessType = $businessType;
    }

    /**
     * @return array<mixed>|null
     */
    public function getRegion(): ?array
    {
        return $this->region;
    }

    /**
     * @param array<mixed>|null $region
     */
    public function setRegion(?array $region): void
    {
        $this->region = $region;
    }

    /**
     * @return array<mixed>|null
     */
    public function getOtherRegion(): ?array
    {
        return $this->otherRegion;
    }

    /**
     * @param array<mixed>|null $otherRegion
     */
    public function setOtherRegion(?array $otherRegion): void
    {
        $this->otherRegion = $otherRegion;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function setArea(float $area): void
    {
        $this->area = $area;
    }

    public function getCapacity(): float
    {
        return $this->capacity;
    }

    public function setCapacity(float $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getUsedCapacity(): float
    {
        return $this->usedCapacity;
    }

    public function setUsedCapacity(float $usedCapacity): void
    {
        $this->usedCapacity = $usedCapacity;
    }

    public function getLocationCount(): int
    {
        return $this->locationCount;
    }

    public function setLocationCount(int $locationCount): void
    {
        $this->locationCount = $locationCount;
    }

    public function getUsedLocationCount(): int
    {
        return $this->usedLocationCount;
    }

    public function setUsedLocationCount(int $usedLocationCount): void
    {
        $this->usedLocationCount = $usedLocationCount;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
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
