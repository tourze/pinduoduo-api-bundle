<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\Depot>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/depot',
    routeName: 'pinduoduo_api_depot'
)]
final class DepotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Depot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('仓库')
            ->setEntityLabelInPlural('仓库管理')
            ->setSearchFields(['depotCode', 'depotName', 'depotAlias', 'contact', 'phone', 'address'])
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
            ->add('depotCode')
            ->add('depotName')
            ->add('type')
            ->add('status')
            ->add('isDefault')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('depotId', '拼多多仓库ID')
            ->setMaxLength(255)
            ->setHelp('拼多多平台仓库ID')
            ->hideOnIndex()
        ;

        yield TextField::new('depotCode', '仓库编码')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('唯一标识仓库的编码')
        ;

        yield TextField::new('depotName', '仓库名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('仓库的正式名称')
        ;

        yield TextField::new('depotAlias', '仓库别名')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('仓库简称或别名')
            ->hideOnIndex()
        ;

        yield TextField::new('contact', '联系人')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('仓库负责人姓名')
            ->hideOnIndex()
        ;

        yield TextField::new('phone', '联系电话')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('仓库联系电话')
            ->hideOnIndex()
        ;

        yield TextField::new('address', '仓库地址')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('详细地址信息')
        ;

        yield IntegerField::new('province', '省份ID')
            ->setHelp('省份标识符')
            ->hideOnIndex()
        ;

        yield IntegerField::new('city', '城市ID')
            ->setHelp('城市标识符')
            ->hideOnIndex()
        ;

        yield IntegerField::new('district', '区县ID')
            ->setHelp('区县标识符')
            ->hideOnIndex()
        ;

        yield TextField::new('zipCode', '邮编')
            ->setRequired(true)
            ->setMaxLength(10)
            ->setHelp('邮政编码')
            ->hideOnIndex()
        ;

        $typeField = EnumField::new('type', '仓库类型');
        $typeField->setEnumCases(DepotTypeEnum::cases());
        $typeField->setHelp('仓库类型分类');
        yield $typeField;

        $businessTypeField = EnumField::new('businessType', '业务类型');
        $businessTypeField->setEnumCases(DepotBusinessTypeEnum::cases());
        $businessTypeField->setHelp('仓库业务类型');
        $businessTypeField->hideOnIndex();
        yield $businessTypeField;

        yield NumberField::new('area', '仓库面积(m²)')
            ->setNumDecimals(2)
            ->setHelp('仓库总面积（平方米）')
            ->hideOnIndex()
        ;

        yield NumberField::new('capacity', '仓库容量(m³)')
            ->setNumDecimals(2)
            ->setHelp('仓库总容量（立方米）')
            ->hideOnIndex()
        ;

        yield NumberField::new('usedCapacity', '已使用容量(m³)')
            ->setNumDecimals(2)
            ->setHelp('已使用仓库容量（立方米）')
            ->hideOnIndex()
        ;

        yield IntegerField::new('locationCount', '货位数量')
            ->setHelp('仓库总货位数量')
            ->hideOnIndex()
        ;

        yield IntegerField::new('usedLocationCount', '已使用货位')
            ->setHelp('已使用的货位数量')
            ->hideOnIndex()
        ;

        yield BooleanField::new('isDefault', '默认仓库')
            ->setHelp('是否为默认仓库')
            ->renderAsSwitch(false)
        ;

        $statusField = EnumField::new('status', '仓库状态');
        $statusField->setEnumCases(DepotStatusEnum::cases());
        $statusField->setHelp('仓库运营状态');
        yield $statusField;

        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('region', '区域覆盖')
                ->setHelp('区域覆盖信息（JSON格式）')
                ->formatValue(function ($value) {
                    return $value ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null;
                })
            ;

            yield TextareaField::new('otherRegion', '其他区域覆盖')
                ->setHelp('其他区域覆盖信息（JSON格式）')
                ->formatValue(function ($value) {
                    return $value ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null;
                })
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
