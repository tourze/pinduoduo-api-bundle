<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Knp\Menu\ItemInterface;
use PinduoduoApiBundle\Controller\Admin\AccountCrudController;
use PinduoduoApiBundle\Controller\Admin\AuthCatCrudController;
use PinduoduoApiBundle\Controller\Admin\AuthLogCrudController;
use PinduoduoApiBundle\Controller\Admin\CategoryCrudController;
use PinduoduoApiBundle\Controller\Admin\CountryCrudController;
use PinduoduoApiBundle\Controller\Admin\DepotCrudController;
use PinduoduoApiBundle\Controller\Admin\DepotPriorityCrudController;
use PinduoduoApiBundle\Controller\Admin\GoodsCrudController;
use PinduoduoApiBundle\Controller\Admin\LogisticsTemplateCrudController;
use PinduoduoApiBundle\Controller\Admin\MallCrudController;
use PinduoduoApiBundle\Controller\Admin\MeasurementCrudController;
use PinduoduoApiBundle\Controller\Admin\OrderCrudController;
use PinduoduoApiBundle\Controller\Admin\SkuCrudController;
use PinduoduoApiBundle\Controller\Admin\SpecCrudController;
use PinduoduoApiBundle\Controller\Admin\StockWareCrudController;
use PinduoduoApiBundle\Controller\Admin\StockWareDepotCrudController;
use PinduoduoApiBundle\Controller\Admin\StockWareSkuCrudController;
use PinduoduoApiBundle\Controller\Admin\StockWareSpecCrudController;
use PinduoduoApiBundle\Controller\Admin\UploadImgCrudController;
use PinduoduoApiBundle\Controller\Admin\VideoCrudController;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    /**
     * 实现MenuProviderInterface的__invoke方法
     */
    public function __invoke(ItemInterface $item): void
    {
        // 在这里构建菜单项到传入的菜单项中
        $menuItems = $this->getMenuItems();

        foreach ($menuItems as $menuItem) {
            // 这里需要根据MenuProviderInterface的实际用途来实现
            // 如果是用于向现有菜单添加项目，则应该使用$item来操作
            // 暂时保持空实现，因为主要功能在getMenuItems中
        }
    }

    /**
     * 获取拼多多API管理菜单项
     *
     * @return array<int, SectionMenuItem|SubMenuItem>
     */
    public function getMenuItems(): array
    {
        return [
            MenuItem::section('拼多多API管理', 'fas fa-store')->setPermission('ROLE_ADMIN'),

            // 基础配置组
            MenuItem::subMenu('基础配置', 'fas fa-cogs')->setSubItems([
                MenuItem::linkToCrud('账户管理', 'fas fa-user-circle', AccountCrudController::class),
                MenuItem::linkToCrud('店铺管理', 'fas fa-store-alt', MallCrudController::class),
                MenuItem::linkToCrud('国家/地区', 'fas fa-globe', CountryCrudController::class),
                MenuItem::linkToCrud('授权类目', 'fas fa-tags', AuthCatCrudController::class),
                MenuItem::linkToCrud('授权记录', 'fas fa-key', AuthLogCrudController::class),
                MenuItem::linkToCrud('运费模板', 'fas fa-truck', LogisticsTemplateCrudController::class),
            ]),

            // 商品管理组
            MenuItem::subMenu('商品管理', 'fas fa-box')->setSubItems([
                MenuItem::linkToCrud('商品类目', 'fas fa-sitemap', CategoryCrudController::class),
                MenuItem::linkToCrud('商品信息', 'fas fa-cube', GoodsCrudController::class),
                MenuItem::linkToCrud('商品规格', 'fas fa-list', SpecCrudController::class),
                MenuItem::linkToCrud('SKU管理', 'fas fa-barcode', SkuCrudController::class),
                MenuItem::linkToCrud('计量单位', 'fas fa-weight', MeasurementCrudController::class),
                MenuItem::linkToCrud('商品图片', 'fas fa-image', UploadImgCrudController::class),
                MenuItem::linkToCrud('商品视频', 'fas fa-video', VideoCrudController::class),
            ]),

            // 库存管理组
            MenuItem::subMenu('库存管理', 'fas fa-warehouse')->setSubItems([
                MenuItem::linkToCrud('仓库信息', 'fas fa-building', DepotCrudController::class),
                MenuItem::linkToCrud('仓库优先级', 'fas fa-sort-numeric-up', DepotPriorityCrudController::class),
                MenuItem::linkToCrud('货品主信息', 'fas fa-boxes', StockWareCrudController::class),
                MenuItem::linkToCrud('仓库分布', 'fas fa-map-marked-alt', StockWareDepotCrudController::class),
                MenuItem::linkToCrud('货品SKU关联', 'fas fa-link', StockWareSkuCrudController::class),
                MenuItem::linkToCrud('货品规格', 'fas fa-clipboard-list', StockWareSpecCrudController::class),
            ]),

            // 订单管理组
            MenuItem::subMenu('订单管理', 'fas fa-shopping-cart')->setSubItems([
                MenuItem::linkToCrud('订单信息', 'fas fa-receipt', OrderCrudController::class),
            ]),

            MenuItem::section('系统管理', 'fas fa-cog')->setPermission('ROLE_SUPER_ADMIN'),
        ];
    }

    /**
     * 获取带权限检查的菜单项
     *
     * @param array<string> $userRoles
     * @return array<int, SectionMenuItem|SubMenuItem>
     */
    public function getMenuItemsForRoles(array $userRoles): array
    {
        $menuItems = $this->getMenuItems();

        // 简化权限检查，实际项目中可以更复杂的权限逻辑
        if (!in_array('ROLE_ADMIN', $userRoles, true) && !in_array('ROLE_SUPER_ADMIN', $userRoles, true)) {
            return [];
        }

        return $menuItems;
    }
}
