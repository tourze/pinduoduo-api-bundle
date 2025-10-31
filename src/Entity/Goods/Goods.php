<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use PinduoduoApiBundle\Enum\Goods\GoodsStatus;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: GoodsRepository::class)]
#[ORM\Table(name: 'ims_pdd_goods', options: ['comment' => '商品'])]
class Goods implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家外部编码'])]
    private ?string $outerGoodsId = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '商品序列编码'])]
    private ?string $goodsSn = null;

    #[Assert\Choice(callback: [GoodsType::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: GoodsType::class, options: ['comment' => '商品类型'])]
    private ?GoodsType $goodsType = null;

    #[ORM\ManyToOne(inversedBy: 'goodsList', cascade: ['persist'])]
    private ?Category $category = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否七天无理由售后'])]
    private ?bool $refundable = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品名称'])]
    private ?string $goodsName = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '商品库存'])]
    private ?int $goodsQuantity = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品图片url'])]
    private ?string $imageUrl = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品是否上架'])]
    private ?bool $onsale = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品是否全新'])]
    private ?bool $secondHand = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '承诺发货时间'])]
    private ?int $shipmentLimitSecond = null;

    #[Assert\Type(type: 'int')]
    #[Assert\Positive]
    #[ORM\Column(nullable: true, options: ['comment' => '成团人数'])]
    private ?int $groupRequiredCustomerNum = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '商品预扣库存'])]
    private ?int $goodsReserveQuantity = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否多sku'])]
    private ?bool $moreSku = null;

    /**
     * @var Collection<int, Sku>
     */
    #[ORM\OneToMany(mappedBy: 'goods', targetEntity: Sku::class, orphanRemoval: true)]
    private Collection $skus;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(nullable: true, options: ['comment' => '坏果包赔'])]
    private ?int $badFruitClaim = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '限购次数'])]
    private ?int $buyLimit = null;

    /**
     * @var array<string, string>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品轮播图列表'])]
    private ?array $carouselGalleryList = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品缩略图'])]
    private ?string $thumbUrl = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '买家自提模版id'])]
    private ?string $maiJiaZiTi = null;

    /**
     * @var int|null 可选范围0-100, 0表示取消，95表示95折，设置需先查询规则接口获取实际可填范围
     */
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(nullable: true, options: ['comment' => '满2件折扣'])]
    private ?int $twoPiecesDiscount = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '团购人数'])]
    private ?string $customerNum = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '卡券类商品属性'])]
    private ?array $elecGoodsAttributes = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否支持假一赔十'])]
    private ?bool $folt = null;

    #[Assert\Choice(callback: [DeliveryType::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: DeliveryType::class, options: ['comment' => '发货方式'])]
    private ?DeliveryType $deliveryType = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品属性列表'])]
    private ?array $goodsProperties = null;

    /**
     * @var array<string, string>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品视频'])]
    private ?array $videoGallery = null;

    /**
     * @var int|null 目前只支持0和365
     */
    #[Assert\Type(type: 'int')]
    #[Assert\Choice(choices: [0, 365])]
    #[ORM\Column(nullable: true, options: ['comment' => '只换不修的天数'])]
    private ?int $zhiHuanBuXiu = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否当日发货'])]
    private ?bool $deliveryOneDay = null;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(nullable: true, options: ['comment' => '海外商品类型'])]
    private ?int $overseaType = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '水果类目温馨提示'])]
    private ?string $warmTips = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品描述'])]
    private ?string $goodsDesc = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '保税仓'])]
    private ?string $warehouse = null;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(nullable: true, options: ['comment' => '第三方商品来源'])]
    private ?int $outSourceType = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '日历商品出行信息'])]
    private ?array $goodsTravelAttr = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '支持全国联保'])]
    private ?bool $quanGuoLianBao = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '参考价格，单位为分'])]
    private ?int $marketPrice = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '单次限量'])]
    private ?int $orderLimit = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Country $country = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否支持正品发票'])]
    private ?bool $invoiceStatus = null;

    #[Assert\Choice(callback: [GoodsStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: GoodsStatus::class, options: ['comment' => '商品状态'])]
    private ?GoodsStatus $status = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否成团预售'])]
    private ?bool $groupPreSale = null;

    #[Assert\Type(type: '\DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '预售时间'])]
    private ?\DateTimeInterface $preSaleTime = null;

    /**
     * @var array<string, string>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品详情图'])]
    private ?array $detailGalleryList = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否sku预售'])]
    private ?bool $skuPreSale = null;

    /**
     * @var string|null 示例：新包装，保证产品的口感和新鲜度。单颗独立小包装，双重营养，1斤家庭分享装，更实惠新疆一级骏枣夹核桃仁。
     */
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短标题'])]
    private ?string $tinyName = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否预售'])]
    private ?bool $preSale = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '第三方商品id'])]
    private ?string $outSourceGoodsId = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '日历商品交易相关信息'])]
    private ?array $goodsTradeAttr = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '缺重包退'])]
    private ?bool $lackOfWeightClaim = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '海外商品信息'])]
    private ?array $overseaGoods = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?LogisticsTemplate $costTemplate = null;

    public function __construct()
    {
        $this->skus = new ArrayCollection();
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
    }

    public function getGoodsSn(): ?string
    {
        return $this->goodsSn;
    }

    public function setGoodsSn(?string $goodsSn): void
    {
        $this->goodsSn = $goodsSn;
    }

    public function getGoodsType(): ?GoodsType
    {
        return $this->goodsType;
    }

    public function setGoodsType(?GoodsType $goodsType): void
    {
        $this->goodsType = $goodsType;
    }

    public function isRefundable(): ?bool
    {
        return $this->refundable;
    }

    public function setRefundable(?bool $refundable): void
    {
        $this->refundable = $refundable;
    }

    public function getGoodsName(): ?string
    {
        return $this->goodsName;
    }

    public function setGoodsName(?string $goodsName): void
    {
        $this->goodsName = $goodsName;
    }

    public function getGoodsQuantity(): ?int
    {
        return $this->goodsQuantity;
    }

    public function setGoodsQuantity(?int $goodsQuantity): void
    {
        $this->goodsQuantity = $goodsQuantity;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function isOnsale(): ?bool
    {
        return $this->onsale;
    }

    public function setOnsale(?bool $onsale): void
    {
        $this->onsale = $onsale;
    }

    public function isSecondHand(): ?bool
    {
        return $this->secondHand;
    }

    public function setSecondHand(?bool $secondHand): void
    {
        $this->secondHand = $secondHand;
    }

    public function getShipmentLimitSecond(): ?int
    {
        return $this->shipmentLimitSecond;
    }

    public function setShipmentLimitSecond(?int $shipmentLimitSecond): void
    {
        $this->shipmentLimitSecond = $shipmentLimitSecond;
    }

    public function getGroupRequiredCustomerNum(): ?int
    {
        return $this->groupRequiredCustomerNum;
    }

    public function setGroupRequiredCustomerNum(?int $groupRequiredCustomerNum): void
    {
        $this->groupRequiredCustomerNum = $groupRequiredCustomerNum;
    }

    public function getGoodsReserveQuantity(): ?int
    {
        return $this->goodsReserveQuantity;
    }

    public function setGoodsReserveQuantity(?int $goodsReserveQuantity): void
    {
        $this->goodsReserveQuantity = $goodsReserveQuantity;
    }

    public function isMoreSku(): ?bool
    {
        return $this->moreSku;
    }

    public function setMoreSku(?bool $moreSku): void
    {
        $this->moreSku = $moreSku;
    }

    /**
     * @return Collection<int, Sku>
     */
    public function getSkus(): Collection
    {
        return $this->skus;
    }

    public function addSku(Sku $sku): static
    {
        if (!$this->skus->contains($sku)) {
            $this->skus->add($sku);
            $sku->setGoods($this);
        }

        return $this;
    }

    public function removeSku(Sku $sku): static
    {
        if ($this->skus->removeElement($sku)) {
            // set the owning side to null (unless already changed)
            if ($sku->getGoods() === $this) {
                $sku->setGoods(null);
            }
        }

        return $this;
    }

    public function getOuterGoodsId(): ?string
    {
        return $this->outerGoodsId;
    }

    public function setOuterGoodsId(?string $outerGoodsId): void
    {
        $this->outerGoodsId = $outerGoodsId;
    }

    public function getBadFruitClaim(): ?int
    {
        return $this->badFruitClaim;
    }

    public function setBadFruitClaim(?int $badFruitClaim): void
    {
        $this->badFruitClaim = $badFruitClaim;
    }

    public function getBuyLimit(): ?int
    {
        return $this->buyLimit;
    }

    public function setBuyLimit(?int $buyLimit): void
    {
        $this->buyLimit = $buyLimit;
    }

    /**
     * @return array<string, string>|null
     */
    public function getCarouselGalleryList(): ?array
    {
        return $this->carouselGalleryList;
    }

    /**
     * @param array<string, string>|null $carouselGalleryList
     */
    public function setCarouselGalleryList(?array $carouselGalleryList): void
    {
        $this->carouselGalleryList = $carouselGalleryList;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        return "{$this->getGoodsName()} #{$this->getId()}";
    }

    public function getMaiJiaZiTi(): ?string
    {
        return $this->maiJiaZiTi;
    }

    public function setMaiJiaZiTi(?string $maiJiaZiTi): void
    {
        $this->maiJiaZiTi = $maiJiaZiTi;
    }

    public function getTwoPiecesDiscount(): ?int
    {
        return $this->twoPiecesDiscount;
    }

    public function setTwoPiecesDiscount(?int $twoPiecesDiscount): void
    {
        $this->twoPiecesDiscount = $twoPiecesDiscount;
    }

    public function getCustomerNum(): ?string
    {
        return $this->customerNum;
    }

    public function setCustomerNum(?string $customerNum): void
    {
        $this->customerNum = $customerNum;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getElecGoodsAttributes(): ?array
    {
        return $this->elecGoodsAttributes;
    }

    /**
     * @param array<string, mixed>|null $elecGoodsAttributes
     */
    public function setElecGoodsAttributes(?array $elecGoodsAttributes): void
    {
        $this->elecGoodsAttributes = $elecGoodsAttributes;
    }

    public function isFolt(): ?bool
    {
        return $this->folt;
    }

    public function setFolt(?bool $folt): void
    {
        $this->folt = $folt;
    }

    public function getDeliveryType(): ?DeliveryType
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(?DeliveryType $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getGoodsProperties(): ?array
    {
        return $this->goodsProperties;
    }

    /**
     * @param array<string, mixed>|null $goodsProperties
     */
    public function setGoodsProperties(?array $goodsProperties): void
    {
        $this->goodsProperties = $goodsProperties;
    }

    /**
     * @return array<string, string>|null
     */
    public function getVideoGallery(): ?array
    {
        return $this->videoGallery;
    }

    /**
     * @param array<string, string>|null $videoGallery
     */
    public function setVideoGallery(?array $videoGallery): void
    {
        $this->videoGallery = $videoGallery;
    }

    public function getZhiHuanBuXiu(): ?int
    {
        return $this->zhiHuanBuXiu;
    }

    public function setZhiHuanBuXiu(?int $zhiHuanBuXiu): void
    {
        $this->zhiHuanBuXiu = $zhiHuanBuXiu;
    }

    public function isDeliveryOneDay(): ?bool
    {
        return $this->deliveryOneDay;
    }

    public function setDeliveryOneDay(?bool $deliveryOneDay): void
    {
        $this->deliveryOneDay = $deliveryOneDay;
    }

    public function getOverseaType(): ?int
    {
        return $this->overseaType;
    }

    public function setOverseaType(?int $overseaType): void
    {
        $this->overseaType = $overseaType;
    }

    public function getWarmTips(): ?string
    {
        return $this->warmTips;
    }

    public function setWarmTips(?string $warmTips): void
    {
        $this->warmTips = $warmTips;
    }

    public function getGoodsDesc(): ?string
    {
        return $this->goodsDesc;
    }

    public function setGoodsDesc(?string $goodsDesc): void
    {
        $this->goodsDesc = $goodsDesc;
    }

    public function getWarehouse(): ?string
    {
        return $this->warehouse;
    }

    public function setWarehouse(?string $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function getOutSourceType(): ?int
    {
        return $this->outSourceType;
    }

    public function setOutSourceType(?int $outSourceType): void
    {
        $this->outSourceType = $outSourceType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getGoodsTravelAttr(): ?array
    {
        return $this->goodsTravelAttr;
    }

    /**
     * @param array<string, mixed>|null $goodsTravelAttr
     */
    public function setGoodsTravelAttr(?array $goodsTravelAttr): void
    {
        $this->goodsTravelAttr = $goodsTravelAttr;
    }

    public function isQuanGuoLianBao(): ?bool
    {
        return $this->quanGuoLianBao;
    }

    public function setQuanGuoLianBao(?bool $quanGuoLianBao): void
    {
        $this->quanGuoLianBao = $quanGuoLianBao;
    }

    public function getMarketPrice(): ?int
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?int $marketPrice): void
    {
        $this->marketPrice = $marketPrice;
    }

    public function getOrderLimit(): ?int
    {
        return $this->orderLimit;
    }

    public function setOrderLimit(?int $orderLimit): void
    {
        $this->orderLimit = $orderLimit;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function isInvoiceStatus(): ?bool
    {
        return $this->invoiceStatus;
    }

    public function setInvoiceStatus(?bool $invoiceStatus): void
    {
        $this->invoiceStatus = $invoiceStatus;
    }

    public function getStatus(): ?GoodsStatus
    {
        return $this->status;
    }

    public function setStatus(?GoodsStatus $status): void
    {
        $this->status = $status;
    }

    public function isGroupPreSale(): ?bool
    {
        return $this->groupPreSale;
    }

    public function setGroupPreSale(?bool $groupPreSale): void
    {
        $this->groupPreSale = $groupPreSale;
    }

    public function getPreSaleTime(): ?\DateTimeInterface
    {
        return $this->preSaleTime;
    }

    public function setPreSaleTime(?\DateTimeInterface $preSaleTime): void
    {
        $this->preSaleTime = $preSaleTime;
    }

    /**
     * @return array<string, string>|null
     */
    public function getDetailGalleryList(): ?array
    {
        return $this->detailGalleryList;
    }

    /**
     * @param array<string, string>|null $detailGalleryList
     */
    public function setDetailGalleryList(?array $detailGalleryList): void
    {
        $this->detailGalleryList = $detailGalleryList;
    }

    public function isSkuPreSale(): ?bool
    {
        return $this->skuPreSale;
    }

    public function setSkuPreSale(?bool $skuPreSale): void
    {
        $this->skuPreSale = $skuPreSale;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getTinyName(): ?string
    {
        return $this->tinyName;
    }

    public function setTinyName(?string $tinyName): void
    {
        $this->tinyName = $tinyName;
    }

    public function isPreSale(): ?bool
    {
        return $this->preSale;
    }

    public function setPreSale(?bool $preSale): void
    {
        $this->preSale = $preSale;
    }

    public function getOutSourceGoodsId(): ?string
    {
        return $this->outSourceGoodsId;
    }

    public function setOutSourceGoodsId(?string $outSourceGoodsId): void
    {
        $this->outSourceGoodsId = $outSourceGoodsId;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getGoodsTradeAttr(): ?array
    {
        return $this->goodsTradeAttr;
    }

    /**
     * @param array<string, mixed>|null $goodsTradeAttr
     */
    public function setGoodsTradeAttr(?array $goodsTradeAttr): void
    {
        $this->goodsTradeAttr = $goodsTradeAttr;
    }

    public function isLackOfWeightClaim(): ?bool
    {
        return $this->lackOfWeightClaim;
    }

    public function setLackOfWeightClaim(?bool $lackOfWeightClaim): void
    {
        $this->lackOfWeightClaim = $lackOfWeightClaim;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getOverseaGoods(): ?array
    {
        return $this->overseaGoods;
    }

    /**
     * @param array<string, mixed>|null $overseaGoods
     */
    public function setOverseaGoods(?array $overseaGoods): void
    {
        $this->overseaGoods = $overseaGoods;
    }

    public function getCostTemplate(): ?LogisticsTemplate
    {
        return $this->costTemplate;
    }

    public function setCostTemplate(?LogisticsTemplate $costTemplate): void
    {
        $this->costTemplate = $costTemplate;
    }
}
