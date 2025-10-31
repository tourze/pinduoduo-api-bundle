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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Enum\Stock\StockWareTypeEnum;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @extends AbstractCrudController<\PinduoduoApiBundle\Entity\Stock\StockWare>
 */
#[AdminCrud(
    routePath: '/pinduoduo-api/stock-ware',
    routeName: 'pinduoduo_api_stock_ware'
)]
final class StockWareCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockWare::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('货品')
            ->setEntityLabelInPlural('货品管理')
            ->setSearchFields(['wareSn', 'wareName', 'specification', 'brand', 'color'])
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('wareId', '拼多多货品ID')
            ->setMaxLength(20)
            ->setHelp('拼多多平台货品ID')
            ->hideOnIndex()
        ;

        yield TextField::new('wareSn', '货品编码')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('唯一标识货品的编码')
        ;

        yield TextField::new('wareName', '货品名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('货品完整名称')
        ;

        yield TextField::new('specification', '规格')
            ->setMaxLength(50)
            ->setHelp('货品规格描述')
            ->hideOnIndex()
        ;

        yield TextField::new('unit', '单位')
            ->setMaxLength(50)
            ->setHelp('货品计量单位')
            ->hideOnIndex()
        ;

        yield TextField::new('brand', '品牌')
            ->setMaxLength(50)
            ->setHelp('货品品牌名称')
        ;

        yield TextField::new('color', '颜色')
            ->setMaxLength(50)
            ->setHelp('货品颜色')
            ->hideOnIndex()
        ;

        yield TextField::new('packing', '包装')
            ->setMaxLength(50)
            ->setHelp('包装方式描述')
            ->hideOnIndex()
        ;

        yield from $this->getTypeField();

        // 重量相关字段
        yield NumberField::new('grossWeight', '毛重(kg)')
            ->setNumDecimals(2)
            ->setHelp('货品毛重（千克）')
            ->hideOnIndex()
        ;

        yield NumberField::new('netWeight', '净重(kg)')
            ->setNumDecimals(2)
            ->setHelp('货品净重（千克）')
            ->hideOnIndex()
        ;

        yield NumberField::new('tareWeight', '皮重(kg)')
            ->setNumDecimals(2)
            ->setHelp('包装重量（千克）')
            ->hideOnIndex()
        ;

        yield NumberField::new('weight', '重量(kg)')
            ->setNumDecimals(2)
            ->setHelp('货品重量（千克）')
            ->hideOnIndex()
        ;

        // 尺寸相关字段
        yield NumberField::new('length', '长度(cm)')
            ->setNumDecimals(2)
            ->setHelp('货品长度（厘米）')
            ->hideOnIndex()
        ;

        yield NumberField::new('width', '宽度(cm)')
            ->setNumDecimals(2)
            ->setHelp('货品宽度（厘米）')
            ->hideOnIndex()
        ;

        yield NumberField::new('height', '高度(cm)')
            ->setNumDecimals(2)
            ->setHelp('货品高度（厘米）')
            ->hideOnIndex()
        ;

        yield NumberField::new('volume', '体积(m³)')
            ->setNumDecimals(2)
            ->setHelp('货品体积（立方米）')
            ->hideOnIndex()
        ;

        // 价格和质量
        yield NumberField::new('price', '价格(元)')
            ->setNumDecimals(2)
            ->setHelp('货品单价（人民币）')
            ->hideOnIndex()
        ;

        yield IntegerField::new('serviceQuality', '服务质量')
            ->setHelp('服务质量评分')
            ->hideOnIndex()
        ;

        yield from $this->getQuantityField();

        yield TextareaField::new('note', '备注')
            ->setMaxLength(255)
            ->setHelp('货品相关备注信息')
            ->hideOnIndex()
        ;

        // 关联字段
        yield AssociationField::new('stockWareSkus', 'SKU关联')
            ->onlyOnDetail()
            ->setHelp('关联的SKU信息')
        ;

        yield AssociationField::new('stockWareDepots', '仓库分布')
            ->onlyOnDetail()
            ->setHelp('货品在各仓库的分布情况')
        ;

        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('wareInfos', '货品信息')
                ->setHelp('货品详细信息（JSON格式）')
                ->formatValue(function ($value) {
                    return $value ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null;
                })
            ;

            yield IntegerField::new('createTime', '创建时间戳')
                ->setHelp('创建时间戳')
                ->formatValue(function ($value) {
                    if (!\is_int($value)) {
                        return '';
                    }

                    return $value > 0 ? date('Y-m-d H:i:s', $value) : '';
                })
            ;

            yield IntegerField::new('updateTime', '更新时间戳')
                ->setHelp('更新时间戳')
                ->formatValue(function ($value) {
                    if (!\is_int($value)) {
                        return '';
                    }

                    return $value > 0 ? date('Y-m-d H:i:s', $value) : '';
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

    /**
     * @return iterable<\EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface>
     */
    private function getTypeField(): iterable
    {
        $typeField = EnumField::new('type', '货品类型');
        $typeField->setEnumCases(StockWareTypeEnum::cases());
        $typeField->setHelp('货品分类类型');
        yield $typeField;
    }

    /**
     * @return iterable<\EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface>
     */
    private function getQuantityField(): iterable
    {
        yield IntegerField::new('quantity', '库存数量')
            ->setHelp('当前库存数量')
            ->formatValue(function (mixed $value, mixed $entity): string {
                if (!\is_int($value)) {
                    return \is_scalar($value) ? (string) $value : '';
                }
                $stockWarning = $value < 10 ? '⚠️ 库存不足' : '';

                return sprintf('%d %s', $value, $stockWarning);
            })
        ;
    }
}
