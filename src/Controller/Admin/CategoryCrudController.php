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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Goods\Category;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Goods\Category>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/category',
    routeName: 'pinduoduo_api_category'
)]
final class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商品类目')
            ->setEntityLabelInPlural('商品类目')
            ->setSearchFields(['name'])
            ->setDefaultSort(['level' => 'ASC', 'name' => 'ASC'])
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
            ->add('name')
            ->add('level')
            ->add('parent')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', '类目ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('name', '类目名称')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('商品分类名称')
        ;

        yield IntegerField::new('level', '类目级别')
            ->setRequired(true)
            ->setHelp('分类层级，数字越大层级越深')
        ;

        yield AssociationField::new('parent', '上级类目')
            ->setHelp('上级分类，如果为空则是顶级分类')
            ->autocomplete()
        ;

        yield AssociationField::new('children', '下级分类')
            ->onlyOnDetail()
            ->setHelp('该分类下的子分类')
        ;

        yield AssociationField::new('specs', '关联规格')
            ->onlyOnDetail()
            ->setHelp('该分类关联的商品规格')
        ;

        yield AssociationField::new('goodsList', '关联商品')
            ->onlyOnDetail()
            ->setHelp('该分类下的商品列表')
        ;

        yield ArrayField::new('catRule', '类目规则')
            ->onlyOnForms()
            ->setHelp('类目商品发布规则配置')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
