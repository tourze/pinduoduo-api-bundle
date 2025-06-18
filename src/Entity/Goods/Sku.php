<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.list.get
 */
#[ORM\Entity(repositoryClass: SkuRepository::class)]
#[ORM\Table(name: 'ims_pdd_sku', options: ['comment' => 'SKU'])]
class Sku implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'skus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Goods $goods = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否在架上'])]
    private ?bool $onsale = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家外部编码'])]
    private ?string $outerSkuId = null;

    #[ORM\Column(nullable: true, options: ['comment' => 'sku库存'])]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true, options: ['comment' => 'sku预扣库存'])]
    private ?int $reserveQuantity = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '规格名称'])]
    private ?string $specName = null;

    #[ORM\Column(nullable: true, options: ['comment' => '规格信息'])]
    private ?array $specDetails = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家编码（sku维度）'])]
    private ?string $outSkuSn = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品团购价格 单位分'])]
    private ?int $multiPrice = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => 'sku预览图'])]
    private ?string $thumbUrl = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'sku预售时间'])]
    private ?\DateTimeInterface $preSaleTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => 'sku送装参数：长度'])]
    private ?int $length = null;

    #[ORM\Column(nullable: true, options: ['comment' => '重量，单位为g'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    private ?array $overseaSku = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $outSourceSkuId = null;

    #[ORM\Column(nullable: true)]
    private ?array $spec = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品单买价格 单位分'])]
    private ?int $price = null;

    #[ORM\Column(nullable: true, options: ['comment' => 'sku购买限制'])]
    private ?int $limitQuantity = null;

    #[ORM\Column(nullable: true, options: ['comment' => 'sku属性'])]
    private ?array $skuProperties = null;

    use TimestampableAware;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getGoods(): ?Goods
    {
        return $this->goods;
    }

    public function setGoods(?Goods $goods): static
    {
        $this->goods = $goods;

        return $this;
    }

    public function isOnsale(): ?bool
    {
        return $this->onsale;
    }

    public function setOnsale(?bool $onsale): static
    {
        $this->onsale = $onsale;

        return $this;
    }

    public function getOuterSkuId(): ?string
    {
        return $this->outerSkuId;
    }

    public function setOuterSkuId(?string $outerSkuId): static
    {
        $this->outerSkuId = $outerSkuId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getReserveQuantity(): ?int
    {
        return $this->reserveQuantity;
    }

    public function setReserveQuantity(?int $reserveQuantity): static
    {
        $this->reserveQuantity = $reserveQuantity;

        return $this;
    }

    public function getSpecName(): ?string
    {
        return $this->specName;
    }

    public function setSpecName(?string $specName): static
    {
        $this->specName = $specName;

        return $this;
    }

    public function getSpecDetails(): ?array
    {
        return $this->specDetails;
    }

    public function setSpecDetails(?array $specDetails): static
    {
        $this->specDetails = $specDetails;

        return $this;
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getSpecName()} #{$this->getId()}";
    }

    public function getOutSkuSn(): ?string
    {
        return $this->outSkuSn;
    }

    public function setOutSkuSn(?string $outSkuSn): static
    {
        $this->outSkuSn = $outSkuSn;

        return $this;
    }

    public function getMultiPrice(): ?int
    {
        return $this->multiPrice;
    }

    public function setMultiPrice(?int $multiPrice): static
    {
        $this->multiPrice = $multiPrice;

        return $this;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): static
    {
        $this->thumbUrl = $thumbUrl;

        return $this;
    }

    public function getPreSaleTime(): ?\DateTimeInterface
    {
        return $this->preSaleTime;
    }

    public function setPreSaleTime(?\DateTimeInterface $preSaleTime): static
    {
        $this->preSaleTime = $preSaleTime;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getOverseaSku(): ?array
    {
        return $this->overseaSku;
    }

    public function setOverseaSku(?array $overseaSku): static
    {
        $this->overseaSku = $overseaSku;

        return $this;
    }

    public function getOutSourceSkuId(): ?string
    {
        return $this->outSourceSkuId;
    }

    public function setOutSourceSkuId(?string $outSourceSkuId): static
    {
        $this->outSourceSkuId = $outSourceSkuId;

        return $this;
    }

    public function getSpec(): ?array
    {
        return $this->spec;
    }

    public function setSpec(?array $spec): static
    {
        $this->spec = $spec;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getLimitQuantity(): ?int
    {
        return $this->limitQuantity;
    }

    public function setLimitQuantity(?int $limitQuantity): static
    {
        $this->limitQuantity = $limitQuantity;

        return $this;
    }

    public function getSkuProperties(): ?array
    {
        return $this->skuProperties;
    }

    public function setSkuProperties(?array $skuProperties): static
    {
        $this->skuProperties = $skuProperties;

        return $this;
    }
}
