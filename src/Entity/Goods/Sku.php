<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.list.get
 */
#[ORM\Entity(repositoryClass: SkuRepository::class)]
#[ORM\Table(name: 'ims_pdd_sku', options: ['comment' => 'SKU'])]
class Sku implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'skus', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Goods $goods = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否在架上'])]
    private ?bool $onsale = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家外部编码'])]
    private ?string $outerSkuId = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => 'sku库存'])]
    private ?int $quantity = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => 'sku预扣库存'])]
    private ?int $reserveQuantity = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '规格名称'])]
    private ?string $specName = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '规格信息'])]
    private ?array $specDetails = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家编码（sku维度）'])]
    private ?string $outSkuSn = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '商品团购价格 单位分'])]
    private ?int $multiPrice = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => 'sku预览图'])]
    private ?string $thumbUrl = null;

    #[Assert\Type(type: '\DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'sku预售时间'])]
    private ?\DateTimeInterface $preSaleTime = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => 'sku送装参数：长度'])]
    private ?int $length = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '重量，单位为g'])]
    private ?int $weight = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '海外SKU信息'])]
    private ?array $overseaSku = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '外部SKU ID'])]
    private ?string $outSourceSkuId = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => 'SKU规格信息'])]
    private ?array $spec = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '商品单买价格 单位分'])]
    private ?int $price = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => 'sku购买限制'])]
    private ?int $limitQuantity = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => 'sku属性'])]
    private ?array $skuProperties = null;

    public function getGoods(): ?Goods
    {
        return $this->goods;
    }

    public function setGoods(?Goods $goods): void
    {
        $this->goods = $goods;
    }

    public function isOnsale(): ?bool
    {
        return $this->onsale;
    }

    public function setOnsale(?bool $onsale): void
    {
        $this->onsale = $onsale;
    }

    public function getOuterSkuId(): ?string
    {
        return $this->outerSkuId;
    }

    public function setOuterSkuId(?string $outerSkuId): void
    {
        $this->outerSkuId = $outerSkuId;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getReserveQuantity(): ?int
    {
        return $this->reserveQuantity;
    }

    public function setReserveQuantity(?int $reserveQuantity): void
    {
        $this->reserveQuantity = $reserveQuantity;
    }

    public function getSpecName(): ?string
    {
        return $this->specName;
    }

    public function setSpecName(?string $specName): void
    {
        $this->specName = $specName;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSpecDetails(): ?array
    {
        return $this->specDetails;
    }

    /**
     * @param array<string, mixed>|null $specDetails
     */
    public function setSpecDetails(?array $specDetails): void
    {
        $this->specDetails = $specDetails;
    }

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        return "{$this->getSpecName()} #{$this->getId()}";
    }

    public function getOutSkuSn(): ?string
    {
        return $this->outSkuSn;
    }

    public function setOutSkuSn(?string $outSkuSn): void
    {
        $this->outSkuSn = $outSkuSn;
    }

    public function getMultiPrice(): ?int
    {
        return $this->multiPrice;
    }

    public function setMultiPrice(?int $multiPrice): void
    {
        $this->multiPrice = $multiPrice;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }

    public function getPreSaleTime(): ?\DateTimeInterface
    {
        return $this->preSaleTime;
    }

    public function setPreSaleTime(?\DateTimeInterface $preSaleTime): void
    {
        $this->preSaleTime = $preSaleTime;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): void
    {
        $this->length = $length;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getOverseaSku(): ?array
    {
        return $this->overseaSku;
    }

    /**
     * @param array<string, mixed>|null $overseaSku
     */
    public function setOverseaSku(?array $overseaSku): void
    {
        $this->overseaSku = $overseaSku;
    }

    public function getOutSourceSkuId(): ?string
    {
        return $this->outSourceSkuId;
    }

    public function setOutSourceSkuId(?string $outSourceSkuId): void
    {
        $this->outSourceSkuId = $outSourceSkuId;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSpec(): ?array
    {
        return $this->spec;
    }

    /**
     * @param array<string, mixed>|null $spec
     */
    public function setSpec(?array $spec): void
    {
        $this->spec = $spec;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function getLimitQuantity(): ?int
    {
        return $this->limitQuantity;
    }

    public function setLimitQuantity(?int $limitQuantity): void
    {
        $this->limitQuantity = $limitQuantity;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSkuProperties(): ?array
    {
        return $this->skuProperties;
    }

    /**
     * @param array<string, mixed>|null $skuProperties
     */
    public function setSkuProperties(?array $skuProperties): void
    {
        $this->skuProperties = $skuProperties;
    }
}
