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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\AuthLog;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\AuthLog>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/auth-log',
    routeName: 'pinduoduo_api_auth_log'
)]
final class AuthLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AuthLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('授权记录')
            ->setEntityLabelInPlural('授权记录')
            ->setSearchFields(['accessToken', 'refreshToken'])
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
            ->add(EntityFilter::new('account', '关联账户'))
            ->add(EntityFilter::new('mall', '关联店铺'))
            ->add(TextFilter::new('accessToken', 'Access Token'))
            ->add(DateTimeFilter::new('tokenExpireTime', 'Token过期时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('account', '关联账户')
            ->setRequired(false)
            ->setHelp('授权关联的拼多多账户')
        ;

        yield AssociationField::new('mall', '关联店铺')
            ->setRequired(false)
            ->setHelp('授权关联的店铺')
        ;

        yield TextField::new('accessToken', 'Access Token')
            ->setMaxLength(120)
            ->setHelp('访问令牌')
            ->hideOnIndex()
        ;

        yield TextField::new('refreshToken', 'Refresh Token')
            ->setMaxLength(120)
            ->setHelp('刷新令牌')
            ->onlyOnForms()
        ;

        yield DateTimeField::new('tokenExpireTime', 'Token过期时间')
            ->setHelp('Access Token 的过期时间')
            ->hideOnIndex()
        ;

        yield ArrayField::new('scope', '授权范围')
            ->setHelp('授权的API访问范围')
            ->onlyOnDetail()
        ;

        yield ArrayField::new('context', '上下文信息')
            ->setHelp('授权过程中的额外信息')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
