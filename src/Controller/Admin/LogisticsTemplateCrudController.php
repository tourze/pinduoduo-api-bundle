<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Enum\CostType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\LogisticsTemplate>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/logistics-template',
    routeName: 'pinduoduo_api_logistics_template'
)]
final class LogisticsTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogisticsTemplate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('运费模板')
            ->setEntityLabelInPlural('运费模板')
            ->setSearchFields(['name'])
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('mall', '关联店铺')
            ->setRequired(true)
            ->setHelp('该运费模板所属的店铺')
        ;

        yield TextField::new('name', '模板名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('运费模板的名称')
        ;

        $costTypeField = EnumField::new('costType', '计费方式');
        $costTypeField->setEnumCases(CostType::cases());
        $costTypeField->setRequired(true);
        $costTypeField->setHelp('运费计算方式');
        yield $costTypeField;

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
            ->add('mall')
            ->add('name')
            ->add('costType')
            ->add('createTime')
            ->add('updateTime')
        ;
    }
}
