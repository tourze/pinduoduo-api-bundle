<?php

namespace PinduoduoApiBundle\Entity\Order;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Enum\Order\AfterSalesStatus;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use PinduoduoApiBundle\Enum\Order\GroupStatus;
use PinduoduoApiBundle\Enum\Order\MktBizType;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Enum\Order\PayType;
use PinduoduoApiBundle\Enum\Order\RefundStatus;
use PinduoduoApiBundle\Enum\Order\RiskControlStatus;
use PinduoduoApiBundle\Enum\Order\StockOutHandleStatus;
use PinduoduoApiBundle\Enum\Order\TradeType;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'ims_pdd_order', options: ['comment' => '订单'])]
class Order implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, options: ['comment' => '订单号'])]
    private ?string $orderSn = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '收件详细地址(加密)'])]
    private ?string $addressEncrypted = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '收件详细地址(打码)'])]
    private ?string $addressMask = null;

    #[Assert\Choice(callback: [AfterSalesStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: AfterSalesStatus::class, options: ['comment' => '售后状态'])]
    private ?AfterSalesStatus $afterSalesStatus = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '买家留言信息'])]
    private ?string $buyerMemo = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(options: ['comment' => '商品分类'])]
    private ?Category $category = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '保税仓库'])]
    private ?string $bondedWarehouse = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(nullable: true, options: ['comment' => '团长免单金额，单位：元'])]
    private ?string $capitalFreeDiscount = null;

    #[Assert\Choice(callback: [ConfirmStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: ConfirmStatus::class, options: ['comment' => '成交状态'])]
    private ?ConfirmStatus $confirmStatus = null;

    #[Assert\Type(type: 'DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '成交时间'])]
    private ?\DateTimeInterface $confirmTime = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '全国联保'])]
    private ?bool $supportNationwideWarranty = null;

    #[Assert\Choice(callback: [GroupStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: GroupStatus::class, options: ['comment' => '成团状态'])]
    private ?GroupStatus $groupStatus = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '顺丰包邮'])]
    private ?bool $freeSf = null;

    /**
     * @var float|null 折扣金额=平台优惠+商家优惠+团长免单优惠金额
     */
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(nullable: true, options: ['comment' => '折扣金额'])]
    private ?float $discountAmount = null;

    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(nullable: true, options: ['comment' => '平台优惠金额'])]
    private ?float $platformDiscount = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '退货包运费'])]
    private ?bool $returnFreightPayer = null;

    #[Assert\Choice(callback: [OrderStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: OrderStatus::class, options: ['comment' => '发货状态'])]
    private ?OrderStatus $orderStatus = null;

    #[Assert\Choice(callback: [RiskControlStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: RiskControlStatus::class, options: ['comment' => '订单审核状态'])]
    private ?RiskControlStatus $riskControlStatus = null;

    #[Assert\Type(type: 'DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '订单承诺发货时间'])]
    private ?\DateTimeInterface $lastShipTime = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否当日发货'])]
    private ?bool $deliveryOneDay = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '卡号信息列表'])]
    private ?array $cardInfoList = null;

    #[Assert\Choice(callback: [RefundStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: RefundStatus::class, options: ['comment' => '售后状态'])]
    private ?RefundStatus $refundStatus = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否缺货'])]
    private ?bool $stockOut = null;

    #[Assert\Type(type: 'DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '确认收货时间'])]
    private ?\DateTimeInterface $receiveTime = null;

    #[Assert\Type(type: 'DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '支付时间'])]
    private ?\DateTimeInterface $payTime = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '赠品列表'])]
    private ?array $giftList = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '发票申请'])]
    private ?bool $invoiceStatus = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '服务费明细列表'])]
    private ?array $serviceFeeDetail = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '订单标签列表'])]
    private ?array $orderTagList = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否是抽奖订单'])]
    private ?bool $luckyFlag = null;

    #[Assert\Choice(callback: [MktBizType::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: MktBizType::class, options: ['comment' => '市场业务类型'])]
    private ?MktBizType $mktBizType = null;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(nullable: true, options: ['comment' => '创建交易时的物流方式'])]
    private ?int $shippingType = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '订单备注'])]
    private ?string $remark = null;

    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[ORM\Column(nullable: true, options: ['comment' => '订单改价折扣金额'])]
    private ?float $orderChangeAmount = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '只换不修'])]
    private ?bool $onlySupportReplace = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '运单号'])]
    private ?string $trackingNumber = null;

    #[Assert\Choice(callback: [PayType::class, 'cases'])]
    #[ORM\Column(length: 40, nullable: true, enumType: PayType::class, options: ['comment' => '支付方式'])]
    private ?PayType $payType = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否多多批发'])]
    private ?bool $duoduoWholesale = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: true, options: ['comment' => '是否为预售商品'])]
    private ?bool $preSale = null;

    #[Assert\Type(type: 'DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发货时间'])]
    private ?\DateTimeInterface $shippingTime = null;

    /**
     * @var float|null 商品金额=商品销售价格*商品数量-订单改价折扣金额
     */
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[ORM\Column(nullable: true, options: ['comment' => '商品金额'])]
    private ?float $goodsAmount = null;

    /**
     * @var float|null 支付金额=商品金额-折扣金额+邮费+服务费
     */
    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[ORM\Column(nullable: true, options: ['comment' => '支付金额'])]
    private ?float $payAmount = null;

    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(nullable: true, options: ['comment' => '商家优惠金额'])]
    private ?float $sellerDiscount = null;

    #[Assert\Choice(callback: [StockOutHandleStatus::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: StockOutHandleStatus::class, options: ['comment' => '缺货处理状态'])]
    private ?StockOutHandleStatus $stockOutHandleStatus = null;

    #[Assert\PositiveOrZero]
    #[Assert\Type(type: 'float')]
    #[ORM\Column(nullable: true, options: ['comment' => '邮费'])]
    private ?float $postage = null;

    #[Assert\Choice(callback: [TradeType::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: TradeType::class, options: ['comment' => '订单类型'])]
    private ?TradeType $tradeType = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '订单商品列表'])]
    private ?array $itemList = null;

    /**
     * @var array<mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    public function getOrderSn(): ?string
    {
        return $this->orderSn;
    }

    public function setOrderSn(string $orderSn): void
    {
        $this->orderSn = $orderSn;
    }

    public function getAddressEncrypted(): ?string
    {
        return $this->addressEncrypted;
    }

    public function setAddressEncrypted(?string $addressEncrypted): void
    {
        $this->addressEncrypted = $addressEncrypted;
    }

    public function getAddressMask(): ?string
    {
        return $this->addressMask;
    }

    public function setAddressMask(?string $addressMask): void
    {
        $this->addressMask = $addressMask;
    }

    public function getAfterSalesStatus(): ?AfterSalesStatus
    {
        return $this->afterSalesStatus;
    }

    public function setAfterSalesStatus(?AfterSalesStatus $afterSalesStatus): void
    {
        $this->afterSalesStatus = $afterSalesStatus;
    }

    public function getBuyerMemo(): ?string
    {
        return $this->buyerMemo;
    }

    public function setBuyerMemo(?string $buyerMemo): void
    {
        $this->buyerMemo = $buyerMemo;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getBondedWarehouse(): ?string
    {
        return $this->bondedWarehouse;
    }

    public function setBondedWarehouse(?string $bondedWarehouse): void
    {
        $this->bondedWarehouse = $bondedWarehouse;
    }

    public function getCapitalFreeDiscount(): ?string
    {
        return $this->capitalFreeDiscount;
    }

    public function setCapitalFreeDiscount(?string $capital_free_discount): void
    {
        $this->capitalFreeDiscount = $capital_free_discount;
    }

    public function getConfirmStatus(): ?ConfirmStatus
    {
        return $this->confirmStatus;
    }

    public function setConfirmStatus(?ConfirmStatus $confirmStatus): void
    {
        $this->confirmStatus = $confirmStatus;
    }

    public function getConfirmTime(): ?\DateTimeInterface
    {
        return $this->confirmTime;
    }

    public function setConfirmTime(?\DateTimeInterface $confirmTime): void
    {
        $this->confirmTime = $confirmTime;
    }

    public function isSupportNationwideWarranty(): ?bool
    {
        return $this->supportNationwideWarranty;
    }

    public function setSupportNationwideWarranty(?bool $supportNationwideWarranty): void
    {
        $this->supportNationwideWarranty = $supportNationwideWarranty;
    }

    public function getGroupStatus(): ?GroupStatus
    {
        return $this->groupStatus;
    }

    public function setGroupStatus(?GroupStatus $groupStatus): void
    {
        $this->groupStatus = $groupStatus;
    }

    public function isFreeSf(): ?bool
    {
        return $this->freeSf;
    }

    public function setFreeSf(?bool $freeSf): void
    {
        $this->freeSf = $freeSf;
    }

    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?float $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    public function getPlatformDiscount(): ?float
    {
        return $this->platformDiscount;
    }

    public function setPlatformDiscount(?float $platformDiscount): void
    {
        $this->platformDiscount = $platformDiscount;
    }

    public function isReturnFreightPayer(): ?bool
    {
        return $this->returnFreightPayer;
    }

    public function setReturnFreightPayer(?bool $returnFreightPayer): void
    {
        $this->returnFreightPayer = $returnFreightPayer;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?OrderStatus $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    public function getRiskControlStatus(): ?RiskControlStatus
    {
        return $this->riskControlStatus;
    }

    public function setRiskControlStatus(?RiskControlStatus $riskControlStatus): void
    {
        $this->riskControlStatus = $riskControlStatus;
    }

    public function getLastShipTime(): ?\DateTimeInterface
    {
        return $this->lastShipTime;
    }

    public function setLastShipTime(?\DateTimeInterface $lastShipTime): void
    {
        $this->lastShipTime = $lastShipTime;
    }

    public function isDeliveryOneDay(): ?bool
    {
        return $this->deliveryOneDay;
    }

    public function setDeliveryOneDay(?bool $deliveryOneDay): void
    {
        $this->deliveryOneDay = $deliveryOneDay;
    }

    /**
     * @return array<mixed>|null
     */
    public function getCardInfoList(): ?array
    {
        return $this->cardInfoList;
    }

    /**
     * @param array<mixed>|null $cardInfoList
     */
    public function setCardInfoList(?array $cardInfoList): void
    {
        $this->cardInfoList = $cardInfoList;
    }

    public function getRefundStatus(): ?RefundStatus
    {
        return $this->refundStatus;
    }

    public function setRefundStatus(?RefundStatus $refundStatus): void
    {
        $this->refundStatus = $refundStatus;
    }

    public function isStockOut(): ?bool
    {
        return $this->stockOut;
    }

    public function getStockOut(): ?bool
    {
        return $this->stockOut;
    }

    public function setStockOut(?bool $stockOut): void
    {
        $this->stockOut = $stockOut;
    }

    public function getReceiveTime(): ?\DateTimeInterface
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeInterface $receiveTime): void
    {
        $this->receiveTime = $receiveTime;
    }

    public function getPayTime(): ?\DateTimeInterface
    {
        return $this->payTime;
    }

    public function setPayTime(?\DateTimeInterface $payTime): void
    {
        $this->payTime = $payTime;
    }

    /**
     * @return array<mixed>|null
     */
    public function getGiftList(): ?array
    {
        return $this->giftList;
    }

    /**
     * @param array<mixed>|null $giftList
     */
    public function setGiftList(?array $giftList): void
    {
        $this->giftList = $giftList;
    }

    public function isInvoiceStatus(): ?bool
    {
        return $this->invoiceStatus;
    }

    public function setInvoiceStatus(?bool $invoiceStatus): void
    {
        $this->invoiceStatus = $invoiceStatus;
    }

    /**
     * @return array<mixed>|null
     */
    public function getServiceFeeDetail(): ?array
    {
        return $this->serviceFeeDetail;
    }

    /**
     * @param array<mixed>|null $serviceFeeDetail
     */
    public function setServiceFeeDetail(?array $serviceFeeDetail): void
    {
        $this->serviceFeeDetail = $serviceFeeDetail;
    }

    /**
     * @return array<mixed>|null
     */
    public function getOrderTagList(): ?array
    {
        return $this->orderTagList;
    }

    /**
     * @param array<mixed>|null $orderTagList
     */
    public function setOrderTagList(?array $orderTagList): void
    {
        $this->orderTagList = $orderTagList;
    }

    public function isLuckyFlag(): ?bool
    {
        return $this->luckyFlag;
    }

    public function setLuckyFlag(?bool $luckyFlag): void
    {
        $this->luckyFlag = $luckyFlag;
    }

    public function getMktBizType(): ?MktBizType
    {
        return $this->mktBizType;
    }

    public function setMktBizType(?MktBizType $mktBizType): void
    {
        $this->mktBizType = $mktBizType;
    }

    public function getShippingType(): ?int
    {
        return $this->shippingType;
    }

    public function setShippingType(?int $shippingType): void
    {
        $this->shippingType = $shippingType;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getOrderChangeAmount(): ?float
    {
        return $this->orderChangeAmount;
    }

    public function setOrderChangeAmount(?float $orderChangeAmount): void
    {
        $this->orderChangeAmount = $orderChangeAmount;
    }

    public function isOnlySupportReplace(): ?bool
    {
        return $this->onlySupportReplace;
    }

    public function setOnlySupportReplace(?bool $onlySupportReplace): void
    {
        $this->onlySupportReplace = $onlySupportReplace;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    public function getPayType(): ?PayType
    {
        return $this->payType;
    }

    public function setPayType(?PayType $payType): void
    {
        $this->payType = $payType;
    }

    public function isDuoduoWholesale(): ?bool
    {
        return $this->duoduoWholesale;
    }

    public function setDuoduoWholesale(?bool $duoduoWholesale): void
    {
        $this->duoduoWholesale = $duoduoWholesale;
    }

    public function isPreSale(): ?bool
    {
        return $this->preSale;
    }

    public function getPreSale(): ?bool
    {
        return $this->preSale;
    }

    public function setPreSale(?bool $preSale): void
    {
        $this->preSale = $preSale;
    }

    public function getShippingTime(): ?\DateTimeInterface
    {
        return $this->shippingTime;
    }

    public function setShippingTime(?\DateTimeInterface $shippingTime): void
    {
        $this->shippingTime = $shippingTime;
    }

    public function getGoodsAmount(): ?float
    {
        return $this->goodsAmount;
    }

    public function setGoodsAmount(?float $goodsAmount): void
    {
        $this->goodsAmount = $goodsAmount;
    }

    public function getPayAmount(): ?float
    {
        return $this->payAmount;
    }

    public function setPayAmount(?float $payAmount): void
    {
        $this->payAmount = $payAmount;
    }

    public function getSellerDiscount(): ?float
    {
        return $this->sellerDiscount;
    }

    public function setSellerDiscount(?float $sellerDiscount): void
    {
        $this->sellerDiscount = $sellerDiscount;
    }

    public function getStockOutHandleStatus(): ?StockOutHandleStatus
    {
        return $this->stockOutHandleStatus;
    }

    public function setStockOutHandleStatus(?StockOutHandleStatus $stockOutHandleStatus): void
    {
        $this->stockOutHandleStatus = $stockOutHandleStatus;
    }

    public function getPostage(): ?float
    {
        return $this->postage;
    }

    public function setPostage(?float $postage): void
    {
        $this->postage = $postage;
    }

    public function getTradeType(): ?TradeType
    {
        return $this->tradeType;
    }

    public function setTradeType(?TradeType $tradeType): void
    {
        $this->tradeType = $tradeType;
    }

    /**
     * @return array<mixed>|null
     */
    public function getItemList(): ?array
    {
        return $this->itemList;
    }

    /**
     * @param array<mixed>|null $itemList
     */
    public function setItemList(?array $itemList): void
    {
        $this->itemList = $itemList;
    }

    /**
     * @return array<mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array<mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
