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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\AuthCat;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\AuthCat>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/auth-cat',
    routeName: 'pinduoduo_api_auth_cat'
)]
final class AuthCatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AuthCat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('授权商品类目')
            ->setEntityLabelInPlural('授权商品类目')
            ->setSearchFields(['catName', 'catId', 'parentCatId'])
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
            ->add(EntityFilter::new('mall', '关联店铺'))
            ->add(TextFilter::new('catId', '类目ID'))
            ->add(TextFilter::new('catName', '类目名称'))
            ->add(TextFilter::new('parentCatId', '上级类目ID'))
            ->add(BooleanFilter::new('leaf', '是否叶子类目'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('mall', '关联店铺')
            ->setRequired(true)
            ->setHelp('该类目所属的店铺')
        ;

        yield TextField::new('catId', '类目ID')
            ->setRequired(true)
            ->setHelp('拼多多类目ID')
        ;

        yield TextField::new('catName', '类目名称')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('拼多多类目名称')
        ;

        yield TextField::new('parentCatId', '上级类目ID')
            ->setHelp('上级分类ID，根类目为0')
        ;

        yield BooleanField::new('leaf', '是否叶子类目')
            ->setHelp('标识是否为叶子类目（最后一级类目）')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
