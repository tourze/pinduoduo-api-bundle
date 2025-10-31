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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Mall>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/mall',
    routeName: 'pinduoduo_api_mall'
)]
final class MallCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mall::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('店铺')
            ->setEntityLabelInPlural('店铺')
            ->setSearchFields(['name', 'description'])
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
            ->add(TextFilter::new('name', '店铺名称'))
            ->add(TextFilter::new('description', '店铺描述'))
            ->add(EntityFilter::new('merchantType', '店铺类型'))
            ->add(EntityFilter::new('mallCharacter', '店铺身份'))
            ->add(BooleanFilter::new('cpsProtocolStatus', '多多进宝协议状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('name', '店铺名称')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('拼多多店铺名称')
        ;

        yield TextareaField::new('description', '店铺描述')
            ->setMaxLength(10000)
            ->setHelp('店铺详细描述')
            ->hideOnIndex()
        ;

        yield UrlField::new('logo', '店铺Logo')
            ->setHelp('店铺logo图片URL')
            ->hideOnIndex()
        ;

        $merchantTypeField = EnumField::new('merchantType', '店铺类型');
        $merchantTypeField->setEnumCases(MerchantType::cases());
        $merchantTypeField->setHelp('店铺的经营类型');
        yield $merchantTypeField;

        $mallCharacterField = EnumField::new('mallCharacter', '店铺身份');
        $mallCharacterField->setEnumCases(MallCharacter::cases());
        $mallCharacterField->setHelp('店铺的身份分类');
        yield $mallCharacterField;

        yield BooleanField::new('cpsProtocolStatus', '多多进宝协议状态')
            ->setHelp('是否签署了多多进宝协议')
        ;

        yield AssociationField::new('authLogs', '授权记录')
            ->onlyOnDetail()
            ->setHelp('该店铺的授权操作记录')
        ;

        yield AssociationField::new('logisticsTemplates', '运费模板')
            ->onlyOnDetail()
            ->setHelp('该店铺配置的运费模板')
        ;

        yield AssociationField::new('videos', '商品视频')
            ->onlyOnDetail()
            ->setHelp('该店铺上传的商品视频')
        ;

        yield AssociationField::new('authCats', '授权类目')
            ->onlyOnDetail()
            ->setHelp('该店铺可发布的商品类目')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }
}
