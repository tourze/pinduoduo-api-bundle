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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\IntegerFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use PinduoduoApiBundle\Enum\Goods\GoodsStatus;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Goods\Goods>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/goods',
    routeName: 'pinduoduo_api_goods'
)]
final class GoodsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Goods::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商品')
            ->setEntityLabelInPlural('商品')
            ->setSearchFields(['goodsName', 'outerGoodsId', 'goodsSn', 'tinyName'])
            ->setDefaultSort(['createTime' => 'DESC'])
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
            ->add(TextFilter::new('goodsName', '商品名称'))
            ->add(TextFilter::new('outerGoodsId', '外部商品ID'))
            ->add(TextFilter::new('goodsSn', '商品编码'))
            ->add(EntityFilter::new('goodsStatus', '商品状态'))
            ->add(EntityFilter::new('goodsType', '商品类型'))
            ->add(EntityFilter::new('deliveryType', '发货类型'))
            ->add(BooleanFilter::new('onSale', '是否在售'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('mall', '店铺')
            ->setRequired(true)
            ->autocomplete()
            ->setHelp('商品所属的拼多多店铺')
        ;

        yield TextField::new('goodsName', '商品名称')
            ->setMaxLength(255)
            ->setHelp('商品的完整名称')
        ;

        yield TextField::new('tinyName', '短标题')
            ->setMaxLength(255)
            ->setHelp('商品的简短标题描述')
            ->hideOnIndex()
        ;

        yield TextField::new('outerGoodsId', '商品ID')
            ->setMaxLength(100)
            ->setHelp('商家自定义的商品编码')
            ->hideOnIndex()
        ;

        yield TextField::new('goodsSn', '商品序列编码')
            ->setMaxLength(64)
            ->setHelp('系统生成的商品序列号')
        ;

        $goodsTypeField = EnumField::new('goodsType', '商品类型');
        $goodsTypeField->setEnumCases(GoodsType::cases());
        $goodsTypeField->setHelp('商品分类类型');
        yield $goodsTypeField;

        $statusField = EnumField::new('status', '商品状态');
        $statusField->setEnumCases(GoodsStatus::cases());
        $statusField->setHelp('商品当前状态');
        yield $statusField;

        yield AssociationField::new('category', '商品类目')
            ->autocomplete()
            ->setHelp('商品所属类目')
        ;

        yield IntegerField::new('goodsQuantity', '商品库存')
            ->setHelp('商品当前库存数量')
        ;

        yield IntegerField::new('goodsReserveQuantity', '商品预扣库存')
            ->setHelp('预扣的库存数量')
            ->hideOnIndex()
        ;

        yield MoneyField::new('marketPrice', '参考价格')
            ->setCurrency('CNY')
            ->setStoredAsCents(true)
            ->setHelp('商品参考价格，单位分')
            ->hideOnIndex()
        ;

        yield UrlField::new('imageUrl', '商品图片')
            ->setHelp('商品主图URL')
        ;

        yield UrlField::new('thumbUrl', '商品缩略图')
            ->setHelp('商品缩略图URL')
            ->hideOnIndex()
        ;

        yield BooleanField::new('onsale', '是否上架')
            ->setHelp('商品是否在售')
        ;

        yield BooleanField::new('refundable', '七天无理由退货')
            ->setHelp('是否支持七天无理由退货')
            ->hideOnIndex()
        ;

        yield BooleanField::new('secondHand', '是否全新')
            ->setHelp('商品是否为全新状态')
            ->hideOnIndex()
        ;

        yield BooleanField::new('moreSku', '是否多SKU')
            ->setHelp('商品是否有多个规格')
            ->hideOnIndex()
        ;

        yield IntegerField::new('shipmentLimitSecond', '承诺发货时间')
            ->setHelp('承诺发货时间（秒）')
            ->hideOnIndex()
        ;

        yield IntegerField::new('groupRequiredCustomerNum', '成团人数')
            ->setHelp('拼团所需最少人数')
            ->hideOnIndex()
        ;

        yield IntegerField::new('buyLimit', '限购次数')
            ->setHelp('每个用户限购次数')
            ->hideOnIndex()
        ;

        yield IntegerField::new('orderLimit', '单次限量')
            ->setHelp('单次购买限制数量')
            ->hideOnIndex()
        ;

        $deliveryTypeField = EnumField::new('deliveryType', '发货方式');
        $deliveryTypeField->setEnumCases(DeliveryType::cases());
        $deliveryTypeField->setHelp('商品发货方式');
        $deliveryTypeField->hideOnIndex();
        yield $deliveryTypeField;

        yield BooleanField::new('invoiceStatus', '支持正品发票')
            ->setHelp('是否支持开具正品发票')
            ->hideOnIndex()
        ;

        yield BooleanField::new('folt', '假一赔十')
            ->setHelp('是否支持假一赔十保障')
            ->hideOnIndex()
        ;

        yield BooleanField::new('preSale', '是否预售')
            ->setHelp('商品是否为预售商品')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('preSaleTime', '预售时间')
            ->setHelp('商品预售开始时间')
            ->hideOnIndex()
        ;

        yield AssociationField::new('country', '国家')
            ->autocomplete()
            ->setHelp('商品来源国家')
            ->hideOnIndex()
        ;

        yield AssociationField::new('costTemplate', '运费模板')
            ->autocomplete()
            ->setHelp('商品使用的运费模板')
            ->hideOnIndex()
        ;

        yield AssociationField::new('skus', 'SKU列表')
            ->onlyOnDetail()
            ->setHelp('商品的所有SKU规格')
        ;

        yield ArrayField::new('carouselGalleryList', '商品轮播图')
            ->onlyOnForms()
            ->setHelp('商品轮播图片列表')
        ;

        yield ArrayField::new('detailGalleryList', '详情图片')
            ->onlyOnForms()
            ->setHelp('商品详情页图片列表')
        ;

        yield ArrayField::new('goodsProperties', '商品属性')
            ->onlyOnForms()
            ->setHelp('商品的属性配置')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
