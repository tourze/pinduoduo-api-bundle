<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\StockWareSpec>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/stock-ware-spec',
    routeName: 'pinduoduo_api_stock_ware_spec'
)]
final class StockWareSpecCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockWareSpec::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('货品规格')
            ->setEntityLabelInPlural('货品规格管理')
            ->setSearchFields(['specId', 'specKey', 'specValue', 'stockWareSku.skuName'])
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('stockWareSku', '关联SKU')
            ->autocomplete()
            ->formatValue(function ($value, $entity) {
                if (!$value instanceof StockWareSku) {
                    return '未关联';
                }

                return sprintf('%s (ID: %s)', $value->getSkuName(), $value->getSkuId());
            })
            ->setHelp('选择关联的SKU，可选字段')
        ;

        yield TextField::new('specId', '规格ID')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('拼多多规格ID')
        ;

        yield TextField::new('specKey', '规格名称')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('规格类型名称（如：颜色、尺寸等）')
        ;

        yield TextField::new('specValue', '规格值')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('具体规格值（如：红色、XL等）')
        ;

        // 在详情页显示完整的规格组合信息
        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('specDisplay', '规格组合')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    if (!$entity instanceof StockWareSpec) {
                        return '';
                    }

                    return sprintf('%s: %s (ID: %s)',
                        $entity->getSpecKey(),
                        $entity->getSpecValue(),
                        $entity->getSpecId()
                    );
                })
                ->setHelp('规格的完整显示格式')
            ;
        }

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
