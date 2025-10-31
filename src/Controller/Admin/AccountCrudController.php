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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Enum\ApplicationType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/account',
    routeName: 'pinduoduo_api_account'
)]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('拼多多账户')
            ->setEntityLabelInPlural('拼多多账户')
            ->setSearchFields(['title', 'clientId'])
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
            ->add(TextFilter::new('title', '应用名称'))
            ->add(TextFilter::new('clientId', 'Client ID'))
            ->add(EntityFilter::new('applicationType', '应用类型'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('title', '应用名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('拼多多开放平台应用名称')
        ;

        yield TextField::new('clientId', 'Client ID')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('拼多多开放平台Client ID')
        ;

        yield TextField::new('clientSecret', 'Client Secret')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('拼多多开放平台Client Secret')
            ->onlyOnForms()
        ;

        $applicationTypeField = EnumField::new('applicationType', '应用类型');
        $applicationTypeField->setEnumCases(ApplicationType::cases());
        $applicationTypeField->setHelp('拼多多应用类型分类');
        yield $applicationTypeField;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('authLogs', '认证日志')
            ->onlyOnDetail()
            ->setHelp('该账户的认证操作记录')
        ;
    }
}
