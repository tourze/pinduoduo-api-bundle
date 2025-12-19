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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\Goods\Sku;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Goods\Sku>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/sku',
    routeName: 'pinduoduo_api_sku'
)]
final class SkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sku::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SKU')
            ->setEntityLabelInPlural('SKU')
            ->setSearchFields(['specName', 'outerSkuId', 'outSkuSn', 'outSourceSkuId'])
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
            ->add(EntityFilter::new('goods', '商品'))
            ->add(TextFilter::new('specName', '规格名称'))
            ->add(NumericFilter::new('quantity', 'SKU库存'))
            ->add(NumericFilter::new('price', '单买价格'))
            ->add(NumericFilter::new('multiPrice', '团购价格'))
            ->add(BooleanFilter::new('onsale', '是否在架'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('goods', '商品')
            ->setRequired(true)
            ->autocomplete()
            ->setHelp('SKU所属的商品')
        ;

        yield TextField::new('specName', '规格名称')
            ->setMaxLength(255)
            ->setHelp('SKU的规格名称描述')
        ;

        yield TextField::new('outerSkuId', '商家外部编码')
            ->setMaxLength(100)
            ->setHelp('商家自定义的SKU编码')
            ->hideOnIndex()
        ;

        yield TextField::new('outSkuSn', '商家编码')
            ->setMaxLength(100)
            ->setHelp('商家定义的SKU序列号')
            ->hideOnIndex()
        ;

        yield TextField::new('outSourceSkuId', '外部SKU ID')
            ->setMaxLength(100)
            ->setHelp('第三方系统的SKU标识')
            ->hideOnIndex()
        ;

        yield IntegerField::new('quantity', 'SKU库存')
            ->setHelp('当前SKU的可用库存数量')
        ;

        yield IntegerField::new('reserveQuantity', 'SKU预扣库存')
            ->setHelp('预扣的SKU库存数量')
            ->hideOnIndex()
        ;

        yield MoneyField::new('price', '单买价格')
            ->setCurrency('CNY')
            ->setStoredAsCents(true)
            ->setHelp('SKU单独购买价格，单位分')
        ;

        yield MoneyField::new('multiPrice', '团购价格')
            ->setCurrency('CNY')
            ->setStoredAsCents(true)
            ->setHelp('SKU拼团价格，单位分')
        ;

        yield BooleanField::new('onsale', '是否在架')
            ->setHelp('SKU是否在售')
        ;

        yield UrlField::new('thumbUrl', 'SKU预览图')
            ->setHelp('SKU商品预览图URL')
            ->hideOnIndex()
        ;

        yield IntegerField::new('limitQuantity', 'SKU购买限制')
            ->setHelp('单次购买该SKU的限制数量')
            ->hideOnIndex()
        ;

        yield IntegerField::new('weight', '重量')
            ->setHelp('SKU重量，单位为克')
            ->hideOnIndex()
        ;

        yield IntegerField::new('length', '长度')
            ->setHelp('SKU送装参数：长度')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('preSaleTime', 'SKU预售时间')
            ->setHelp('SKU预售开始时间')
            ->hideOnIndex()
        ;

        yield ArrayField::new('specDetails', '规格详情')
            ->onlyOnForms()
            ->setHelp('SKU的详细规格信息')
        ;

        yield ArrayField::new('spec', 'SKU规格信息')
            ->onlyOnForms()
            ->setHelp('SKU的规格配置信息')
        ;

        yield ArrayField::new('skuProperties', 'SKU属性')
            ->onlyOnForms()
            ->setHelp('SKU的属性配置')
        ;

        yield ArrayField::new('overseaSku', '海外SKU信息')
            ->onlyOnForms()
            ->setHelp('海外商品的SKU相关信息')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
