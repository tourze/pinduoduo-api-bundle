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
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use PinduoduoApiBundle\Entity\UploadImg;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\UploadImg>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/upload-img',
    routeName: 'pinduoduo_api_upload_img'
)]
final class UploadImgCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UploadImg::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商品图片')
            ->setEntityLabelInPlural('商品图片')
            ->setSearchFields(['file', 'url'])
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
            ->setHelp('该图片所属的店铺')
        ;

        yield TextField::new('file', '原始图片文件')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('商品原始图片文件名或路径')
        ;

        yield UrlField::new('url', '图片URL')
            ->setHelp('上传后的图片访问URL')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
