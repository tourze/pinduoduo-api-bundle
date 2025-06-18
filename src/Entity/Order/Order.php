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
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'ims_pdd_order', options: ['comment' => '订单'])]
class Order implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(length: 64, options: ['comment' => '订单号'])]
    private ?string $orderSn = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '收件详细地址(加密)'])]
    private ?string $addressEncrypted = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '收件详细地址(打码)'])]
    private ?string $addressMask = null;

    #[ORM\Column(nullable: true, enumType: AfterSalesStatus::class, options: ['comment' => '售后状态'])]
    private ?AfterSalesStatus $afterSalesStatus = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '买家留言信息'])]
    private ?string $buyerMemo = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(options: ['comment' => '商品分类'])]
    private ?Category $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bondedWarehouse = null;

    #[ORM\Column(nullable: true, options: ['comment' => '团长免单金额，单位：元'])]
    private ?float $capitalFreeDiscount = null;

    #[ORM\Column(nullable: true, enumType: ConfirmStatus::class, options: ['comment' => '成交状态'])]
    private ?ConfirmStatus $confirmStatus = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '成交时间'])]
    private ?\DateTimeInterface $confirmTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '全国联保'])]
    private ?bool $supportNationwideWarranty = null;

    #[ORM\Column(nullable: true, enumType: GroupStatus::class, options: ['comment' => '成团状态'])]
    private ?GroupStatus $groupStatus = null;

    #[ORM\Column(nullable: true, options: ['comment' => '顺丰包邮'])]
    private ?bool $freeSf = null;

    /**
     * @var float|null 折扣金额=平台优惠+商家优惠+团长免单优惠金额
     */
    #[ORM\Column(nullable: true, options: ['comment' => '折扣金额'])]
    private ?float $discountAmount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '平台优惠金额'])]
    private ?float $platformDiscount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '退货包运费'])]
    private ?bool $returnFreightPayer = null;

    #[ORM\Column(nullable: true, enumType: OrderStatus::class, options: ['comment' => '发货状态'])]
    private ?OrderStatus $orderStatus = null;

    #[ORM\Column(nullable: true, enumType: RiskControlStatus::class, options: ['comment' => '订单审核状态'])]
    private ?RiskControlStatus $riskControlStatus = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '订单承诺发货时间'])]
    private ?\DateTimeInterface $lastShipTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否当日发货'])]
    private ?bool $deliveryOneDay = null;

    #[ORM\Column(nullable: true, options: ['comment' => '卡号信息列表'])]
    private ?array $cardInfoList = null;

    #[ORM\Column(nullable: true, enumType: RefundStatus::class, options: ['comment' => '售后状态'])]
    private ?RefundStatus $refundStatus = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否缺货'])]
    private ?bool $stockOut = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '确认收货时间'])]
    private ?\DateTimeInterface $receiveTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '支付时间'])]
    private ?\DateTimeInterface $payTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '赠品列表'])]
    private ?array $giftList = null;

    #[ORM\Column(nullable: true, options: ['comment' => '发票申请'])]
    private ?bool $invoiceStatus = null;

    #[ORM\Column(nullable: true, options: ['comment' => '服务费明细列表'])]
    private ?array $serviceFeeDetail = null;

    #[ORM\Column(nullable: true, options: ['comment' => '订单标签列表'])]
    private ?array $orderTagList = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否是抽奖订单'])]
    private ?bool $luckyFlag = null;

    #[ORM\Column(nullable: true, enumType: MktBizType::class, options: ['comment' => '市场业务类型'])]
    private ?MktBizType $mktBizType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '创建交易时的物流方式'])]
    private ?int $shippingType = null;

    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '订单备注'])]
    private ?string $remark = null;

    #[ORM\Column(nullable: true, options: ['comment' => '订单改价折扣金额'])]
    private ?float $orderChangeAmount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '只换不修'])]
    private ?bool $onlySupportReplace = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '运单号'])]
    private ?string $trackingNumber = null;

    #[ORM\Column(length: 40, nullable: true, enumType: PayType::class, options: ['comment' => '支付方式'])]
    private ?PayType $payType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否多多批发'])]
    private ?bool $duoduoWholesale = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否为预售商品'])]
    private ?bool $preSale = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发货时间'])]
    private ?\DateTimeInterface $shippingTime = null;

    /**
     * @var float|null 商品金额=商品销售价格*商品数量-订单改价折扣金额
     */
    #[ORM\Column(nullable: true, options: ['comment' => '商品金额'])]
    private ?float $goodsAmount = null;

    /**
     * @var float|null 支付金额=商品金额-折扣金额+邮费+服务费
     */
    #[ORM\Column(nullable: true, options: ['comment' => '支付金额'])]
    private ?float $payAmount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商家优惠金额'])]
    private ?float $sellerDiscount = null;

    #[ORM\Column(nullable: true, enumType: StockOutHandleStatus::class, options: ['comment' => '缺货处理状态'])]
    private ?StockOutHandleStatus $stockOutHandleStatus = null;

    #[ORM\Column(nullable: true, options: ['comment' => '邮费'])]
    private ?float $postage = null;

    #[ORM\Column(nullable: true, enumType: TradeType::class, options: ['comment' => '订单类型'])]
    private ?TradeType $tradeType = null;

    #[ORM\Column(nullable: true)]
    private ?array $itemList = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOrderSn(): ?string
    {
        return $this->orderSn;
    }

    public function setOrderSn(string $orderSn): static
    {
        $this->orderSn = $orderSn;

        return $this;
    }

    public function getAddressEncrypted(): ?string
    {
        return $this->addressEncrypted;
    }

    public function setAddressEncrypted(?string $addressEncrypted): static
    {
        $this->addressEncrypted = $addressEncrypted;

        return $this;
    }

    public function getAddressMask(): ?string
    {
        return $this->addressMask;
    }

    public function setAddressMask(?string $addressMask): static
    {
        $this->addressMask = $addressMask;

        return $this;
    }

    public function getAfterSalesStatus(): ?AfterSalesStatus
    {
        return $this->afterSalesStatus;
    }

    public function setAfterSalesStatus(?AfterSalesStatus $afterSalesStatus): static
    {
        $this->afterSalesStatus = $afterSalesStatus;

        return $this;
    }

    public function getBuyerMemo(): ?string
    {
        return $this->buyerMemo;
    }

    public function setBuyerMemo(?string $buyerMemo): static
    {
        $this->buyerMemo = $buyerMemo;

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

    public function getBondedWarehouse(): ?string
    {
        return $this->bondedWarehouse;
    }

    public function setBondedWarehouse(?string $bondedWarehouse): static
    {
        $this->bondedWarehouse = $bondedWarehouse;

        return $this;
    }

    public function getCapitalFreeDiscount(): ?string
    {
        return $this->capitalFreeDiscount;
    }

    public function setCapitalFreeDiscount(?string $capital_free_discount): static
    {
        $this->capitalFreeDiscount = $capital_free_discount;

        return $this;
    }

    public function getConfirmStatus(): ?ConfirmStatus
    {
        return $this->confirmStatus;
    }

    public function setConfirmStatus(?ConfirmStatus $confirmStatus): static
    {
        $this->confirmStatus = $confirmStatus;

        return $this;
    }

    public function getConfirmTime(): ?\DateTimeInterface
    {
        return $this->confirmTime;
    }

    public function setConfirmTime(?\DateTimeInterface $confirmTime): static
    {
        $this->confirmTime = $confirmTime;

        return $this;
    }

    public function isSupportNationwideWarranty(): ?bool
    {
        return $this->supportNationwideWarranty;
    }

    public function setSupportNationwideWarranty(?bool $supportNationwideWarranty): static
    {
        $this->supportNationwideWarranty = $supportNationwideWarranty;

        return $this;
    }

    public function getGroupStatus(): ?GroupStatus
    {
        return $this->groupStatus;
    }

    public function setGroupStatus(?GroupStatus $groupStatus): static
    {
        $this->groupStatus = $groupStatus;

        return $this;
    }

    public function isFreeSf(): ?bool
    {
        return $this->freeSf;
    }

    public function setFreeSf(?bool $freeSf): static
    {
        $this->freeSf = $freeSf;

        return $this;
    }

    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?float $discountAmount): static
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getPlatformDiscount(): ?float
    {
        return $this->platformDiscount;
    }

    public function setPlatformDiscount(?float $platformDiscount): static
    {
        $this->platformDiscount = $platformDiscount;

        return $this;
    }

    public function isReturnFreightPayer(): ?bool
    {
        return $this->returnFreightPayer;
    }

    public function setReturnFreightPayer(?bool $returnFreightPayer): static
    {
        $this->returnFreightPayer = $returnFreightPayer;

        return $this;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getRiskControlStatus(): ?RiskControlStatus
    {
        return $this->riskControlStatus;
    }

    public function setRiskControlStatus(?RiskControlStatus $riskControlStatus): static
    {
        $this->riskControlStatus = $riskControlStatus;

        return $this;
    }

    public function getLastShipTime(): ?\DateTimeInterface
    {
        return $this->lastShipTime;
    }

    public function setLastShipTime(?\DateTimeInterface $lastShipTime): static
    {
        $this->lastShipTime = $lastShipTime;

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

    public function getCardInfoList(): ?array
    {
        return $this->cardInfoList;
    }

    public function setCardInfoList(?array $cardInfoList): static
    {
        $this->cardInfoList = $cardInfoList;

        return $this;
    }

    public function getRefundStatus(): ?RefundStatus
    {
        return $this->refundStatus;
    }

    public function setRefundStatus(?RefundStatus $refundStatus): static
    {
        $this->refundStatus = $refundStatus;

        return $this;
    }

    public function isStockOut(): ?bool
    {
        return $this->stockOut;
    }

    public function setStockOut(?bool $stockOut): static
    {
        $this->stockOut = $stockOut;

        return $this;
    }

    public function getReceiveTime(): ?\DateTimeInterface
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeInterface $receiveTime): static
    {
        $this->receiveTime = $receiveTime;

        return $this;
    }

    public function getPayTime(): ?\DateTimeInterface
    {
        return $this->payTime;
    }

    public function setPayTime(?\DateTimeInterface $payTime): static
    {
        $this->payTime = $payTime;

        return $this;
    }

    public function getGiftList(): ?array
    {
        return $this->giftList;
    }

    public function setGiftList(?array $giftList): static
    {
        $this->giftList = $giftList;

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

    public function getServiceFeeDetail(): ?array
    {
        return $this->serviceFeeDetail;
    }

    public function setServiceFeeDetail(?array $serviceFeeDetail): static
    {
        $this->serviceFeeDetail = $serviceFeeDetail;

        return $this;
    }

    public function getOrderTagList(): ?array
    {
        return $this->orderTagList;
    }

    public function setOrderTagList(?array $orderTagList): static
    {
        $this->orderTagList = $orderTagList;

        return $this;
    }

    public function isLuckyFlag(): ?bool
    {
        return $this->luckyFlag;
    }

    public function setLuckyFlag(?bool $luckyFlag): static
    {
        $this->luckyFlag = $luckyFlag;

        return $this;
    }

    public function getMktBizType(): ?MktBizType
    {
        return $this->mktBizType;
    }

    public function setMktBizType(?MktBizType $mktBizType): static
    {
        $this->mktBizType = $mktBizType;

        return $this;
    }

    public function getShippingType(): ?int
    {
        return $this->shippingType;
    }

    public function setShippingType(?int $shippingType): static
    {
        $this->shippingType = $shippingType;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;

        return $this;
    }

    public function getOrderChangeAmount(): ?float
    {
        return $this->orderChangeAmount;
    }

    public function setOrderChangeAmount(?float $orderChangeAmount): static
    {
        $this->orderChangeAmount = $orderChangeAmount;

        return $this;
    }

    public function isOnlySupportReplace(): ?bool
    {
        return $this->onlySupportReplace;
    }

    public function setOnlySupportReplace(?bool $onlySupportReplace): static
    {
        $this->onlySupportReplace = $onlySupportReplace;

        return $this;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): static
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    public function getPayType(): ?PayType
    {
        return $this->payType;
    }

    public function setPayType(?PayType $payType): static
    {
        $this->payType = $payType;

        return $this;
    }

    public function isDuoduoWholesale(): ?bool
    {
        return $this->duoduoWholesale;
    }

    public function setDuoduoWholesale(?bool $duoduoWholesale): static
    {
        $this->duoduoWholesale = $duoduoWholesale;

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

    public function getShippingTime(): ?\DateTimeInterface
    {
        return $this->shippingTime;
    }

    public function setShippingTime(?\DateTimeInterface $shippingTime): static
    {
        $this->shippingTime = $shippingTime;

        return $this;
    }

    public function getGoodsAmount(): ?float
    {
        return $this->goodsAmount;
    }

    public function setGoodsAmount(?float $goodsAmount): static
    {
        $this->goodsAmount = $goodsAmount;

        return $this;
    }

    public function getPayAmount(): ?float
    {
        return $this->payAmount;
    }

    public function setPayAmount(?float $payAmount): static
    {
        $this->payAmount = $payAmount;

        return $this;
    }

    public function getSellerDiscount(): ?float
    {
        return $this->sellerDiscount;
    }

    public function setSellerDiscount(?float $sellerDiscount): static
    {
        $this->sellerDiscount = $sellerDiscount;

        return $this;
    }

    public function getStockOutHandleStatus(): ?StockOutHandleStatus
    {
        return $this->stockOutHandleStatus;
    }

    public function setStockOutHandleStatus(?StockOutHandleStatus $stockOutHandleStatus): static
    {
        $this->stockOutHandleStatus = $stockOutHandleStatus;

        return $this;
    }

    public function getPostage(): ?float
    {
        return $this->postage;
    }

    public function setPostage(?float $postage): static
    {
        $this->postage = $postage;

        return $this;
    }

    public function getTradeType(): ?TradeType
    {
        return $this->tradeType;
    }

    public function setTradeType(?TradeType $tradeType): static
    {
        $this->tradeType = $tradeType;

        return $this;
    }

    public function getItemList(): ?array
    {
        return $this->itemList;
    }

    public function setItemList(?array $itemList): static
    {
        $this->itemList = $itemList;

        return $this;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }
    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
