<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Entity\Goods\Spec;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Entity\Stock\StockWare;
use PinduoduoApiBundle\Entity\Stock\StockWareDepot;
use PinduoduoApiBundle\Entity\Stock\StockWareSku;
use PinduoduoApiBundle\Entity\Stock\StockWareSpec;
use PinduoduoApiBundle\Entity\UploadImg;
use PinduoduoApiBundle\Entity\Video;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/pinduoduo-api/admin', routeName: 'pinduoduo_api_admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('@EasyAdmin/welcome.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('拼多多API管理')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('首页', 'fa fa-home');

        yield MenuItem::section('账号管理');
        yield MenuItem::linkToCrud('应用账号', 'fa fa-key', Account::class);
        yield MenuItem::linkToCrud('店铺', 'fa fa-store', Mall::class);
        yield MenuItem::linkToCrud('授权日志', 'fa fa-history', AuthLog::class);

        yield MenuItem::section('商品管理');
        yield MenuItem::linkToCrud('商品', 'fa fa-box', Goods::class);
        yield MenuItem::linkToCrud('商品SKU', 'fa fa-list', Sku::class);
        yield MenuItem::linkToCrud('商品类目', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('授权类目', 'fa fa-folder-open', AuthCat::class);
        yield MenuItem::linkToCrud('商品规格', 'fa fa-tags', Spec::class);
        yield MenuItem::linkToCrud('计量单位', 'fa fa-ruler', Measurement::class);

        yield MenuItem::section('订单管理');
        yield MenuItem::linkToCrud('订单', 'fa fa-shopping-cart', Order::class);

        yield MenuItem::section('库存管理');
        yield MenuItem::linkToCrud('仓库', 'fa fa-warehouse', Depot::class);
        yield MenuItem::linkToCrud('仓库优先级', 'fa fa-sort-numeric-down', DepotPriority::class);
        yield MenuItem::linkToCrud('库存商品', 'fa fa-boxes', StockWare::class);
        yield MenuItem::linkToCrud('库存商品SKU', 'fa fa-cubes', StockWareSku::class);
        yield MenuItem::linkToCrud('库存商品规格', 'fa fa-cog', StockWareSpec::class);
        yield MenuItem::linkToCrud('库存商品仓库', 'fa fa-building', StockWareDepot::class);

        yield MenuItem::section('基础数据');
        yield MenuItem::linkToCrud('物流模板', 'fa fa-truck', LogisticsTemplate::class);
        yield MenuItem::linkToCrud('国家地区', 'fa fa-globe', Country::class);
        yield MenuItem::linkToCrud('上传图片', 'fa fa-image', UploadImg::class);
        yield MenuItem::linkToCrud('上传视频', 'fa fa-video', Video::class);
    }
}
