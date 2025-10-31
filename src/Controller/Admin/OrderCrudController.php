<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\Order\Order;
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
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Order\Order>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/order',
    routeName: 'pinduoduo_api_order'
)]
final class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('拼多多订单')
            ->setEntityLabelInPlural('拼多多订单')
            ->setSearchFields(['orderSn', 'trackingNumber', 'buyerMemo', 'remark'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('orderSn', '订单号'))
            ->add(ChoiceFilter::new('orderStatus', '发货状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), OrderStatus::cases()),
                    OrderStatus::cases()
                )))
            ->add(ChoiceFilter::new('confirmStatus', '成交状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), ConfirmStatus::cases()),
                    ConfirmStatus::cases()
                )))
            ->add(ChoiceFilter::new('payType', '支付方式')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), PayType::cases()),
                    PayType::cases()
                )))
            ->add(NumericFilter::new('payAmount', '支付金额'))
            ->add(BooleanFilter::new('stockOut', '是否缺货'))
            ->add(BooleanFilter::new('preSale', '是否预售'))
            ->add(DateTimeFilter::new('payTime', '支付时间'))
            ->add(DateTimeFilter::new('shippingTime', '发货时间'))
            ->add(DateTimeFilter::new('confirmTime', '成交时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('orderSn', '订单号')
            ->setRequired(true)
            ->setMaxLength(64)
            ->setHelp('拼多多订单唯一标识号')
        ;

        yield TextField::new('trackingNumber', '运单号')
            ->setMaxLength(64)
            ->setHelp('物流运单号')
            ->hideOnIndex()
        ;

        // 订单状态相关字段 - 在Index页面隐藏Virtual字段以避免"Inaccessible"错误

        $orderStatusField = EnumField::new('orderStatus', '发货状态');
        $orderStatusField->setEnumCases(OrderStatus::cases());
        $orderStatusField->setHelp('订单发货处理状态');
        $orderStatusField->setRequired(false);
        yield $orderStatusField;

        $confirmStatusField = EnumField::new('confirmStatus', '成交状态');
        $confirmStatusField->setEnumCases(ConfirmStatus::cases());
        $confirmStatusField->setHelp('订单成交确认状态');
        $confirmStatusField->setRequired(false);
        yield $confirmStatusField;

        $groupStatusField = EnumField::new('groupStatus', '成团状态');
        $groupStatusField->setEnumCases(GroupStatus::cases());
        $groupStatusField->setHelp('拼团状态');
        $groupStatusField->hideOnIndex();
        yield $groupStatusField;

        $afterSalesStatusField = EnumField::new('afterSalesStatus', '售后状态');
        $afterSalesStatusField->setEnumCases(AfterSalesStatus::cases());
        $afterSalesStatusField->setHelp('售后处理状态');
        $afterSalesStatusField->hideOnIndex();
        yield $afterSalesStatusField;

        $refundStatusField = EnumField::new('refundStatus', '退款状态');
        $refundStatusField->setEnumCases(RefundStatus::cases());
        $refundStatusField->setHelp('退款处理状态');
        $refundStatusField->hideOnIndex();
        yield $refundStatusField;

        $riskControlStatusField = EnumField::new('riskControlStatus', '风控状态');
        $riskControlStatusField->setEnumCases(RiskControlStatus::cases());
        $riskControlStatusField->setHelp('订单审核风控状态');
        $riskControlStatusField->hideOnIndex();
        yield $riskControlStatusField;

        $stockOutHandleStatusField = EnumField::new('stockOutHandleStatus', '缺货处理状态');
        $stockOutHandleStatusField->setEnumCases(StockOutHandleStatus::cases());
        $stockOutHandleStatusField->setHelp('缺货情况处理状态');
        $stockOutHandleStatusField->hideOnIndex();
        yield $stockOutHandleStatusField;

        // 金额相关字段
        yield MoneyField::new('payAmount', '支付金额')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('买家实际支付金额')
            ->setRequired(false)
        ;

        yield MoneyField::new('goodsAmount', '商品金额')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('商品总价金额')
            ->hideOnIndex()
        ;

        yield MoneyField::new('discountAmount', '折扣金额')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('总折扣优惠金额')
            ->hideOnIndex()
        ;

        yield MoneyField::new('platformDiscount', '平台优惠')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('平台优惠折扣金额')
            ->hideOnIndex()
        ;

        yield MoneyField::new('sellerDiscount', '商家优惠')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('商家优惠折扣金额')
            ->hideOnIndex()
        ;

        yield MoneyField::new('postage', '邮费')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('物流运费金额')
            ->hideOnIndex()
        ;

        yield MoneyField::new('orderChangeAmount', '改价折扣')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('订单改价折扣金额')
            ->hideOnIndex()
        ;

        // 支付相关
        $payTypeField = EnumField::new('payType', '支付方式');
        $payTypeField->setEnumCases(PayType::cases());
        $payTypeField->setHelp('买家选择的支付方式');
        $payTypeField->setRequired(false);
        yield $payTypeField;

        yield DateTimeField::new('payTime', '支付时间')
            ->setHelp('买家完成支付的时间')
            ->setRequired(false)
        ;

        // 时间相关字段
        yield DateTimeField::new('confirmTime', '成交时间')
            ->setHelp('订单确认成交时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('shippingTime', '发货时间')
            ->setHelp('商家发货时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('receiveTime', '收货时间')
            ->setHelp('买家确认收货时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('lastShipTime', '承诺发货时间')
            ->setHelp('订单承诺最晚发货时间')
            ->hideOnIndex()
        ;

        // 业务类型
        $tradeTypeField = EnumField::new('tradeType', '订单类型');
        $tradeTypeField->setEnumCases(TradeType::cases());
        $tradeTypeField->setHelp('订单业务类型分类');
        $tradeTypeField->hideOnIndex();
        yield $tradeTypeField;

        $mktBizTypeField = EnumField::new('mktBizType', '市场业务类型');
        $mktBizTypeField->setEnumCases(MktBizType::cases());
        $mktBizTypeField->setHelp('市场营销业务类型');
        $mktBizTypeField->hideOnIndex();
        yield $mktBizTypeField;

        // 地址信息
        yield TextField::new('addressMask', '收货地址(脱敏)')
            ->setMaxLength(255)
            ->setHelp('收货详细地址脱敏显示')
            ->onlyOnDetail()
        ;

        yield TextField::new('addressEncrypted', '收货地址(加密)')
            ->setMaxLength(255)
            ->setHelp('收货详细地址加密存储')
            ->onlyOnForms()
        ;

        // 布尔字段
        yield BooleanField::new('stockOut', '缺货')
            ->setHelp('是否存在缺货情况')
            ->setRequired(false)
        ;

        yield BooleanField::new('preSale', '预售')
            ->setHelp('是否为预售商品订单')
            ->setRequired(false)
        ;

        yield BooleanField::new('duoduoWholesale', '多多批发')
            ->setHelp('是否为多多批发订单')
            ->hideOnIndex()
        ;

        yield BooleanField::new('luckyFlag', '抽奖订单')
            ->setHelp('是否为抽奖活动订单')
            ->hideOnIndex()
        ;

        yield BooleanField::new('freeSf', '顺丰包邮')
            ->setHelp('是否享受顺丰包邮服务')
            ->hideOnIndex()
        ;

        yield BooleanField::new('returnFreightPayer', '退货包运费')
            ->setHelp('退货时是否包含运费')
            ->hideOnIndex()
        ;

        yield BooleanField::new('deliveryOneDay', '当日发货')
            ->setHelp('是否承诺当日发货')
            ->hideOnIndex()
        ;

        yield BooleanField::new('onlySupportReplace', '只换不修')
            ->setHelp('售后是否只支持换货不支持维修')
            ->hideOnIndex()
        ;

        yield BooleanField::new('supportNationwideWarranty', '全国联保')
            ->setHelp('是否提供全国联保服务')
            ->hideOnIndex()
        ;

        yield BooleanField::new('invoiceStatus', '发票申请')
            ->setHelp('是否申请开具发票')
            ->hideOnIndex()
        ;

        // 关联字段 - 由于可能为null，完全隐藏以避免显示"Inaccessible"
        yield AssociationField::new('category', '商品分类')
            ->setHelp('订单商品所属分类')
            ->onlyOnDetail()
        ;

        // 其他字段
        yield IntegerField::new('shippingType', '物流方式')
            ->setHelp('创建交易时选择的物流配送方式')
            ->hideOnIndex()
        ;

        yield TextField::new('bondedWarehouse', '保税仓库')
            ->setMaxLength(100)
            ->setHelp('跨境商品保税仓库信息')
            ->hideOnIndex()
        ;

        yield TextField::new('capitalFreeDiscount', '团长免单金额')
            ->setMaxLength(255)
            ->setHelp('团长免单优惠金额')
            ->hideOnIndex()
        ;

        yield TextareaField::new('buyerMemo', '买家留言')
            ->setMaxLength(255)
            ->setHelp('买家下单时的留言信息')
            ->hideOnIndex()
        ;

        yield TextareaField::new('remark', '订单备注')
            ->setMaxLength(1000)
            ->setHelp('订单相关备注信息')
            ->hideOnIndex()
        ;

        // 复杂数据字段
        yield ArrayField::new('itemList', '商品列表')
            ->setHelp('订单包含的商品明细列表')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('giftList', '赠品列表')
            ->setHelp('订单包含的赠品列表')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('cardInfoList', '卡号信息')
            ->setHelp('虚拟商品卡号信息列表')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('serviceFeeDetail', '服务费明细')
            ->setHelp('订单服务费用详细信息')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('orderTagList', '订单标签')
            ->setHelp('订单相关标签列表')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('context', '上下文信息')
            ->setHelp('订单额外上下文数据')
            ->onlyOnDetail()
        ;

        // 时间戳字段
        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
