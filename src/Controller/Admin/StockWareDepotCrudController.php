<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\StockWareDepot>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/stock-ware-depot',
    routeName: 'pinduoduo_api_stock_ware_depot'
)]
final class StockWareDepotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockWareDepot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('货品仓库库存')
            ->setEntityLabelInPlural('货品仓库库存管理')
            ->setSearchFields(['stockWare.wareName', 'stockWare.wareSn', 'depot.depotName', 'depot.depotCode', 'locationCode'])
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
            ->add(EntityFilter::new('depot', '仓库'))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields();
        yield from $this->getAssociationFields();
        yield from $this->getQuantityFields();
        yield from $this->getThresholdFields();

        if (Crud::PAGE_DETAIL === $pageName) {
            yield from $this->getUtilizationFields();
        }

        yield from $this->getAdditionalFields();
        yield from $this->getTimestampFields();
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicFields(): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnDetail();
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(): iterable
    {
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

        yield AssociationField::new('depot', '关联仓库')
            ->setRequired(true)
            ->autocomplete()
            ->formatValue(function ($value, $entity) {
                if (!$value instanceof Depot) {
                    return null;
                }

                return sprintf('%s (%s)', $value->getDepotName(), $value->getDepotCode());
            })
            ->setHelp('选择关联的仓库')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getQuantityFields(): iterable
    {
        yield IntegerField::new('availableQuantity', '可用库存')
            ->setHelp('当前可用库存数量')
            ->formatValue(function (mixed $value, mixed $entity): string {
                if (!\is_int($value)) {
                    return \is_scalar($value) ? (string) $value : '';
                }
                $warning = $value < 10 ? ' ⚠️' : '';

                return sprintf('%d%s', $value, $warning);
            })
        ;

        yield IntegerField::new('occupiedQuantity', '占用库存')
            ->setHelp('已被占用的库存数量')
            ->hideOnIndex()
        ;

        yield IntegerField::new('lockedQuantity', '锁定库存')
            ->setHelp('被锁定的库存数量')
            ->hideOnIndex()
        ;

        yield IntegerField::new('onwayQuantity', '在途库存')
            ->setHelp('在途中的库存数量')
            ->hideOnIndex()
        ;

        yield IntegerField::new('totalQuantity', '总库存')
            ->setHelp('该货品在此仓库的总库存量')
            ->formatValue(function (mixed $value, mixed $entity): string {
                $quantity = \is_int($value) ? $value : (\is_numeric($value) ? (int) $value : 0);

                return sprintf('%d 件', $quantity);
            })
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getThresholdFields(): iterable
    {
        yield NumberField::new('warningThreshold', '预警阈值')
            ->setNumDecimals(2)
            ->setHelp('库存预警阈值，低于此值时提醒补货')
            ->hideOnIndex()
        ;

        yield NumberField::new('upperLimit', '库存上限')
            ->setNumDecimals(2)
            ->setHelp('该货品在此仓库的最大存储量')
            ->hideOnIndex()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getUtilizationFields(): iterable
    {
        yield TextField::new('utilizationRate', '库存利用率')
            ->onlyOnDetail()
            ->formatValue(function (mixed $value, mixed $entity): string {
                if (!$entity instanceof \PinduoduoApiBundle\Entity\Stock\StockWareDepot) {
                    return '数据类型错误';
                }
                if ($entity->getUpperLimit() > 0) {
                    $rate = ($entity->getTotalQuantity() / $entity->getUpperLimit()) * 100;
                    $rateColor = $rate > 90 ? '🔴' : ($rate > 70 ? '🟡' : '🟢');

                    return sprintf('%s %.2f%% (%d/%d)', $rateColor, $rate, $entity->getTotalQuantity(), (int) $entity->getUpperLimit());
                }

                return '未设置上限';
            })
            ->setHelp('当前库存占库存上限的比例')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAdditionalFields(): iterable
    {
        yield TextField::new('locationCode', '货位编码')
            ->setMaxLength(255)
            ->setHelp('货品在仓库中的具体货位编码')
            ->hideOnIndex()
        ;

        yield TextareaField::new('note', '备注')
            ->setMaxLength(255)
            ->setHelp('库存相关备注信息')
            ->hideOnIndex()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields(): iterable
    {
        yield DateTimeField::new('createTime', '创建时间')->onlyOnDetail();
        yield DateTimeField::new('updateTime', '更新时间')->onlyOnDetail();
    }
}
