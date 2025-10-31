<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\DepotPriority>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/depot-priority',
    routeName: 'pinduoduo_api_depot_priority'
)]
final class DepotPriorityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DepotPriority::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('仓库优先级')
            ->setEntityLabelInPlural('仓库优先级管理')
            ->setSearchFields(['depotCode', 'depotName', 'provinceId', 'cityId', 'districtId'])
            ->setDefaultSort(['priority' => 'ASC'])
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

        yield TextField::new('depotCode', '仓库编码')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('仓库编码标识')
            ->hideOnIndex()
        ;

        yield TextField::new('depotId', '拼多多仓库ID')
            ->setMaxLength(255)
            ->setHelp('拼多多平台仓库ID')
            ->hideOnIndex()
        ;

        yield TextField::new('depotName', '仓库名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('仓库名称')
        ;

        yield IntegerField::new('provinceId', '省份ID')
            ->setRequired(true)
            ->setHelp('省份标识符')
        ;

        yield IntegerField::new('cityId', '城市ID')
            ->setRequired(true)
            ->setHelp('城市标识符')
        ;

        yield IntegerField::new('districtId', '区县ID')
            ->setRequired(true)
            ->setHelp('区县标识符')
            ->hideOnIndex()
        ;

        yield IntegerField::new('priority', '优先级')
            ->setRequired(true)
            ->setHelp('优先级数值，数字越小优先级越高')
            ->formatValue(fn ($value, $entity): string => $this->formatPriorityValue($value))
        ;

        $priorityTypeField = EnumField::new('priorityType', '优先级类型');
        $priorityTypeField->setEnumCases(DepotPriorityTypeEnum::cases());
        $priorityTypeField->setHelp('优先级分类类型');
        $priorityTypeField->hideOnIndex();
        yield $priorityTypeField;

        $statusField = EnumField::new('status', '状态');
        $statusField->setEnumCases(DepotStatusEnum::cases());
        $statusField->setHelp('优先级配置状态');
        yield $statusField;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('depot')
            ->add('depotCode')
            ->add('depotName')
            ->add('provinceId')
            ->add('cityId')
            ->add('priority')
            ->add('priorityType')
            ->add('status')
        ;
    }

    /**
     * 格式化优先级显示值
     */
    private function formatPriorityValue(mixed $value): string
    {
        if (!\is_int($value)) {
            return \is_scalar($value) ? (string) $value : '';
        }

        $label = $this->getPriorityLabel($value);

        return sprintf('%d (优先级: %s)', $value, $label);
    }

    /**
     * 获取优先级标签
     */
    private function getPriorityLabel(int $value): string
    {
        if (0 === $value) {
            return '最高';
        }

        if ($value < 10) {
            return '高';
        }

        if ($value < 50) {
            return '中';
        }

        return '低';
    }
}
