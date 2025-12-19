<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\StockWareSku>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/stock-ware-sku',
    routeName: 'pinduoduo_api_stock_ware_sku'
)]
final class StockWareSkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockWareSku::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('货品SKU关联')
            ->setEntityLabelInPlural('货品SKU关联管理')
            ->setSearchFields(['stockWare.wareName', 'stockWare.wareSn', 'goodsId', 'skuId', 'skuName'])
            ->setDefaultSort(['createTime' => 'DESC'])
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
            ->add(EntityFilter::new('stockWare', '货品'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('stockWare', '关联货品')
            ->setRequired(true)
            ->autocomplete()
            ->formatValue(function ($value, $entity) {
                if (!$value instanceof StockWare) {
                    return null;
                }

                return sprintf('%s (%s)', $value->getWareName(), $value->getWareSn());
            })
            ->setHelp('选择关联的货品')
        ;

        yield TextField::new('goodsId', '商品ID')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('拼多多商品ID')
            ->hideOnIndex()
        ;

        yield TextField::new('skuId', 'SKU ID')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('拼多多SKU ID')
        ;

        yield TextField::new('skuName', 'SKU名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('SKU名称描述')
        ;

        yield IntegerField::new('quantity', '库存数量')
            ->setHelp('该SKU的库存数量')
            ->formatValue(function (mixed $value, mixed $entity): string {
                if (!\is_int($value)) {
                    return \is_scalar($value) ? (string) $value : '';
                }
                if (!$entity instanceof \PinduoduoApiBundle\Entity\Stock\StockWareSku) {
                    return sprintf('%d', $value);
                }
                $status = '';
                if (!$entity->isExistWare()) {
                    $status .= ' ❌ 无货品';
                }
                if (!$entity->isOnsale()) {
                    $status .= ' ⏸️ 未在售';
                }
                if ($value < 10) {
                    $status .= ' ⚠️ 库存不足';
                }

                return sprintf('%d%s', $value, $status);
            })
        ;

        yield BooleanField::new('existWare', '存在货品')
            ->setHelp('是否存在对应的货品')
            ->renderAsSwitch(false)
        ;

        yield BooleanField::new('isOnsale', '是否在售')
            ->setHelp('该SKU是否正在销售')
            ->renderAsSwitch(false)
        ;

        yield ChoiceField::new('status', '关联状态')
            ->setChoices([
                '正常' => 1,
                '停用' => 2,
            ])
            ->setHelp('SKU与货品的关联状态')
            ->renderExpanded(false)
            ->formatValue(function ($value, $entity) {
                return match ($value) {
                    1 => '🟢 正常',
                    2 => '🔴 停用',
                    default => '❓ 未知',
                };
            })
        ;

        yield AssociationField::new('specs', '规格信息')
            ->onlyOnDetail()
            ->setHelp('该SKU的规格详细信息')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
