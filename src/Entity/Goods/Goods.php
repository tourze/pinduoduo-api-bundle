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
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '商品')]
#[ORM\Entity(repositoryClass: GoodsRepository::class)]
#[ORM\Table(name: 'ims_pdd_goods', options: ['comment' => '商品'])]
class Goods implements \Stringable
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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '商家外部编码'])]
    private ?string $outerGoodsId = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '商品序列编码'])]
    private ?string $goodsSn = null;

    #[ORM\Column(nullable: true, enumType: GoodsType::class, options: ['comment' => '商品类型'])]
    private ?GoodsType $goodsType = null;

    #[ORM\ManyToOne(inversedBy: 'goodsList')]
    private ?Category $category = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否七天无理由售后'])]
    private ?bool $refundable = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品名称'])]
    private ?string $goodsName = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品库存'])]
    private ?int $goodsQuantity = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品图片url'])]
    private ?string $imageUrl = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品是否上架'])]
    private ?bool $onsale = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品是否全新'])]
    private ?bool $secondHand = null;

    #[ORM\Column(nullable: true, options: ['comment' => '承诺发货时间'])]
    private ?int $shipmentLimitSecond = null;

    #[ORM\Column(nullable: true, options: ['comment' => '成团人数'])]
    private ?int $groupRequiredCustomerNum = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品预扣库存'])]
    private ?int $goodsReserveQuantity = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否多sku'])]
    private ?bool $moreSku = null;

    #[ORM\OneToMany(mappedBy: 'goods', targetEntity: Sku::class, orphanRemoval: true)]
    private Collection $skus;

    #[ORM\Column(nullable: true, options: ['comment' => '坏果包赔'])]
    private ?int $badFruitClaim = null;

    #[ORM\Column(nullable: true, options: ['comment' => '限购次数'])]
    private ?int $buyLimit = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品轮播图列表'])]
    private ?array $carouselGalleryList = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品缩略图'])]
    private ?string $thumbUrl = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '买家自提模版id'])]
    private ?string $maiJiaZiTi = null;

    /**
     * @var int|null 可选范围0-100, 0表示取消，95表示95折，设置需先查询规则接口获取实际可填范围
     */
    #[ORM\Column(nullable: true, options: ['comment' => '满2件折扣'])]
    private ?int $twoPiecesDiscount = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['comment' => '团购人数'])]
    private ?string $customerNum = null;

    #[ORM\Column(nullable: true, options: ['comment' => '卡券类商品属性'])]
    private ?array $elecGoodsAttributes = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否支持假一赔十'])]
    private ?bool $folt = null;

    #[ORM\Column(nullable: true, enumType: DeliveryType::class, options: ['comment' => '发货方式'])]
    private ?DeliveryType $deliveryType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品属性列表'])]
    private ?array $goodsProperties = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品视频'])]
    private ?array $videoGallery = null;

    /**
     * @var int|null 目前只支持0和365
     */
    #[ORM\Column(nullable: true, options: ['comment' => '只换不修的天数'])]
    private ?int $zhiHuanBuXiu = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否当日发货'])]
    private ?bool $deliveryOneDay = null;

    #[ORM\Column(nullable: true)]
    private ?int $overseaType = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '水果类目温馨提示'])]
    private ?string $warmTips = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '商品描述'])]
    private ?string $goodsDesc = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '保税仓'])]
    private ?string $warehouse = null;

    #[ORM\Column(nullable: true, options: ['comment' => '第三方商品来源'])]
    private ?int $outSourceType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '日历商品出行信息'])]
    private ?array $goodsTravelAttr = null;

    #[ORM\Column(nullable: true, options: ['comment' => '支持全国联保'])]
    private ?bool $quanGuoLianBao = null;

    #[ORM\Column(nullable: true, options: ['comment' => '参考价格，单位为分'])]
    private ?int $marketPrice = null;

    #[ORM\Column(nullable: true, options: ['comment' => '单次限量'])]
    private ?int $orderLimit = null;

    #[ORM\ManyToOne]
    private ?Country $country = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否支持正品发票'])]
    private ?bool $invoiceStatus = null;

    #[ORM\Column(nullable: true, enumType: GoodsStatus::class, options: ['comment' => '商品状态'])]
    private ?GoodsStatus $status = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否成团预售'])]
    private ?bool $groupPreSale = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '预售时间'])]
    private ?\DateTimeInterface $preSaleTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商品详情图'])]
    private ?array $detailGalleryList = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否sku预售'])]
    private ?bool $skuPreSale = null;

    /**
     * @var string|null 示例：新包装，保证产品的口感和新鲜度。单颗独立小包装，双重营养，1斤家庭分享装，更实惠新疆一级骏枣夹核桃仁。
     */
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短标题'])]
    private ?string $tinyName = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否预售'])]
    private ?bool $preSale = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '第三方商品id'])]
    private ?string $outSourceGoodsId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '日历商品交易相关信息'])]
    private ?array $goodsTradeAttr = null;

    #[ORM\Column(nullable: true, options: ['comment' => '缺重包退'])]
    private ?bool $lackOfWeightClaim = null;

    #[ORM\Column(nullable: true)]
    private ?array $overseaGoods = null;

    #[ORM\ManyToOne]
    private ?LogisticsTemplate $costTemplate = null;

    use TimestampableAware;

    public function __construct()
    {
        $this->skus = new ArrayCollection();
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): static
    {
        $this->mall = $mall;

        return $this;
    }

    public function getGoodsSn(): ?string
    {
        return $this->goodsSn;
    }

    public function setGoodsSn(?string $goodsSn): static
    {
        $this->goodsSn = $goodsSn;

        return $this;
    }

    public function getGoodsType(): ?GoodsType
    {
        return $this->goodsType;
    }

    public function setGoodsType(?GoodsType $goodsType): static
    {
        $this->goodsType = $goodsType;

        return $this;
    }

    public function isRefundable(): ?bool
    {
        return $this->refundable;
    }

    public function setRefundable(?bool $refundable): static
    {
        $this->refundable = $refundable;

        return $this;
    }

    public function getGoodsName(): ?string
    {
        return $this->goodsName;
    }

    public function setGoodsName(?string $goodsName): static
    {
        $this->goodsName = $goodsName;

        return $this;
    }

    public function getGoodsQuantity(): ?int
    {
        return $this->goodsQuantity;
    }

    public function setGoodsQuantity(?int $goodsQuantity): static
    {
        $this->goodsQuantity = $goodsQuantity;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

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

    public function isSecondHand(): ?bool
    {
        return $this->secondHand;
    }

    public function setSecondHand(?bool $secondHand): static
    {
        $this->secondHand = $secondHand;

        return $this;
    }

    public function getShipmentLimitSecond(): ?int
    {
        return $this->shipmentLimitSecond;
    }

    public function setShipmentLimitSecond(?int $shipmentLimitSecond): static
    {
        $this->shipmentLimitSecond = $shipmentLimitSecond;

        return $this;
    }

    public function getGroupRequiredCustomerNum(): ?int
    {
        return $this->groupRequiredCustomerNum;
    }

    public function setGroupRequiredCustomerNum(?int $groupRequiredCustomerNum): static
    {
        $this->groupRequiredCustomerNum = $groupRequiredCustomerNum;

        return $this;
    }

    public function getGoodsReserveQuantity(): ?int
    {
        return $this->goodsReserveQuantity;
    }

    public function setGoodsReserveQuantity(?int $goodsReserveQuantity): static
    {
        $this->goodsReserveQuantity = $goodsReserveQuantity;

        return $this;
    }

    public function isMoreSku(): ?bool
    {
        return $this->moreSku;
    }

    public function setMoreSku(?bool $moreSku): static
    {
        $this->moreSku = $moreSku;

        return $this;
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

    public function setOuterGoodsId(?string $outerGoodsId): static
    {
        $this->outerGoodsId = $outerGoodsId;

        return $this;
    }

    public function getBadFruitClaim(): ?int
    {
        return $this->badFruitClaim;
    }

    public function setBadFruitClaim(?int $badFruitClaim): static
    {
        $this->badFruitClaim = $badFruitClaim;

        return $this;
    }

    public function getBuyLimit(): ?int
    {
        return $this->buyLimit;
    }

    public function setBuyLimit(?int $buyLimit): static
    {
        $this->buyLimit = $buyLimit;

        return $this;
    }

    public function getCarouselGalleryList(): ?array
    {
        return $this->carouselGalleryList;
    }

    public function setCarouselGalleryList(?array $carouselGalleryList): static
    {
        $this->carouselGalleryList = $carouselGalleryList;

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

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getGoodsName()} #{$this->getId()}";
    }

    public function getMaiJiaZiTi(): ?string
    {
        return $this->maiJiaZiTi;
    }

    public function setMaiJiaZiTi(?string $maiJiaZiTi): static
    {
        $this->maiJiaZiTi = $maiJiaZiTi;

        return $this;
    }

    public function getTwoPiecesDiscount(): ?int
    {
        return $this->twoPiecesDiscount;
    }

    public function setTwoPiecesDiscount(?int $twoPiecesDiscount): static
    {
        $this->twoPiecesDiscount = $twoPiecesDiscount;

        return $this;
    }

    public function getCustomerNum(): ?string
    {
        return $this->customerNum;
    }

    public function setCustomerNum(?string $customerNum): static
    {
        $this->customerNum = $customerNum;

        return $this;
    }

    public function getElecGoodsAttributes(): ?array
    {
        return $this->elecGoodsAttributes;
    }

    public function setElecGoodsAttributes(?array $elecGoodsAttributes): static
    {
        $this->elecGoodsAttributes = $elecGoodsAttributes;

        return $this;
    }

    public function isFolt(): ?bool
    {
        return $this->folt;
    }

    public function setFolt(?bool $folt): static
    {
        $this->folt = $folt;

        return $this;
    }

    public function getDeliveryType(): ?DeliveryType
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(?DeliveryType $deliveryType): static
    {
        $this->deliveryType = $deliveryType;

        return $this;
    }

    public function getGoodsProperties(): ?array
    {
        return $this->goodsProperties;
    }

    public function setGoodsProperties(?array $goodsProperties): static
    {
        $this->goodsProperties = $goodsProperties;

        return $this;
    }

    public function getVideoGallery(): ?array
    {
        return $this->videoGallery;
    }

    public function setVideoGallery(?array $videoGallery): static
    {
        $this->videoGallery = $videoGallery;

        return $this;
    }

    public function getZhiHuanBuXiu(): ?int
    {
        return $this->zhiHuanBuXiu;
    }

    public function setZhiHuanBuXiu(?int $zhiHuanBuXiu): static
    {
        $this->zhiHuanBuXiu = $zhiHuanBuXiu;

        return $this;
    }

    public function isDeliveryOneDay(): ?bool
    {
        return $this->deliveryOneDay;
    }

    public function setDeliveryOneDay(?bool $deliveryOneDay): static
    {
        $this->deliveryOneDay = $deliveryOneDay;

        return $this;
    }

    public function getOverseaType(): ?int
    {
        return $this->overseaType;
    }

    public function setOverseaType(?int $overseaType): static
    {
        $this->overseaType = $overseaType;

        return $this;
    }

    public function getWarmTips(): ?string
    {
        return $this->warmTips;
    }

    public function setWarmTips(?string $warmTips): static
    {
        $this->warmTips = $warmTips;

        return $this;
    }

    public function getGoodsDesc(): ?string
    {
        return $this->goodsDesc;
    }

    public function setGoodsDesc(?string $goodsDesc): static
    {
        $this->goodsDesc = $goodsDesc;

        return $this;
    }

    public function getWarehouse(): ?string
    {
        return $this->warehouse;
    }

    public function setWarehouse(?string $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getOutSourceType(): ?int
    {
        return $this->outSourceType;
    }

    public function setOutSourceType(?int $outSourceType): static
    {
        $this->outSourceType = $outSourceType;

        return $this;
    }

    public function getGoodsTravelAttr(): ?array
    {
        return $this->goodsTravelAttr;
    }

    public function setGoodsTravelAttr(?array $goodsTravelAttr): static
    {
        $this->goodsTravelAttr = $goodsTravelAttr;

        return $this;
    }

    public function isQuanGuoLianBao(): ?bool
    {
        return $this->quanGuoLianBao;
    }

    public function setQuanGuoLianBao(?bool $quanGuoLianBao): static
    {
        $this->quanGuoLianBao = $quanGuoLianBao;

        return $this;
    }

    public function getMarketPrice(): ?int
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?int $marketPrice): static
    {
        $this->marketPrice = $marketPrice;

        return $this;
    }

    public function getOrderLimit(): ?int
    {
        return $this->orderLimit;
    }

    public function setOrderLimit(?int $orderLimit): static
    {
        $this->orderLimit = $orderLimit;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function isInvoiceStatus(): ?bool
    {
        return $this->invoiceStatus;
    }

    public function setInvoiceStatus(?bool $invoiceStatus): static
    {
        $this->invoiceStatus = $invoiceStatus;

        return $this;
    }

    public function getStatus(): ?GoodsStatus
    {
        return $this->status;
    }

    public function setStatus(?GoodsStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isGroupPreSale(): ?bool
    {
        return $this->groupPreSale;
    }

    public function setGroupPreSale(?bool $groupPreSale): static
    {
        $this->groupPreSale = $groupPreSale;

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

    public function getDetailGalleryList(): ?array
    {
        return $this->detailGalleryList;
    }

    public function setDetailGalleryList(?array $detailGalleryList): static
    {
        $this->detailGalleryList = $detailGalleryList;

        return $this;
    }

    public function isSkuPreSale(): ?bool
    {
        return $this->skuPreSale;
    }

    public function setSkuPreSale(?bool $skuPreSale): static
    {
        $this->skuPreSale = $skuPreSale;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTinyName(): ?string
    {
        return $this->tinyName;
    }

    public function setTinyName(?string $tinyName): static
    {
        $this->tinyName = $tinyName;

        return $this;
    }

    public function isPreSale(): ?bool
    {
        return $this->preSale;
    }

    public function setPreSale(?bool $preSale): static
    {
        $this->preSale = $preSale;

        return $this;
    }

    public function getOutSourceGoodsId(): ?string
    {
        return $this->outSourceGoodsId;
    }

    public function setOutSourceGoodsId(?string $outSourceGoodsId): static
    {
        $this->outSourceGoodsId = $outSourceGoodsId;

        return $this;
    }

    public function getGoodsTradeAttr(): ?array
    {
        return $this->goodsTradeAttr;
    }

    public function setGoodsTradeAttr(?array $goodsTradeAttr): static
    {
        $this->goodsTradeAttr = $goodsTradeAttr;

        return $this;
    }

    public function isLackOfWeightClaim(): ?bool
    {
        return $this->lackOfWeightClaim;
    }

    public function setLackOfWeightClaim(?bool $lackOfWeightClaim): static
    {
        $this->lackOfWeightClaim = $lackOfWeightClaim;

        return $this;
    }

    public function getOverseaGoods(): ?array
    {
        return $this->overseaGoods;
    }

    public function setOverseaGoods(?array $overseaGoods): static
    {
        $this->overseaGoods = $overseaGoods;

        return $this;
    }

    public function getCostTemplate(): ?LogisticsTemplate
    {
        return $this->costTemplate;
    }

    public function setCostTemplate(?LogisticsTemplate $costTemplate): static
    {
        $this->costTemplate = $costTemplate;

        return $this;
    }
}
