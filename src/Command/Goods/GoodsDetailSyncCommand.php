<?php

namespace PinduoduoApiBundle\Command\Goods;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use PinduoduoApiBundle\Repository\CountryRepository;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.commit.detail.get
 */
#[AsCommand(name: GoodsDetailSyncCommand::NAME, description: '同步商品详情')]
class GoodsDetailSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-goods-detail';

    public function __construct(
        private readonly SdkService $sdkService,
        private readonly GoodsRepository $goodsRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CountryRepository $countryRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly LogisticsTemplateRepository $templateRepository,
        private readonly SkuRepository $skuRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('goodsId', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $goods = $this->goodsRepository->find($input->getArgument('goodsId'));
        if ($goods === null) {
            return Command::FAILURE;
        }
        $mall = $goods->getMall();

        // 推广优化、打单、进销存、商品优化分析、搬家上货、虚拟商家后台系统、企业ERP、商家后台系统、订单处理、电子凭证商家后台系统、跨境企业ERP报关版
        $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::搬家上货);
        if ($sdk === null) {
            return Command::FAILURE;
        }

        $response = $sdk->auth_api->request('pdd.goods.detail.get', [
            'goods_id' => $goods->getId(),
        ]);
        if (!isset($response['goods_detail_get_response'])) {
            return Command::FAILURE;
        }
        $response = $response['goods_detail_get_response'];

        $goods->setOuterGoodsId($response['outer_goods_id']);
        $goods->setGoodsName($response['goods_name']);
        $goods->setMaiJiaZiTi($response['mai_jia_zi_ti']);
        $goods->setTwoPiecesDiscount($response['two_pieces_discount']);
        $goods->setShipmentLimitSecond($response['shipment_limit_second']);
        $goods->setCustomerNum($response['customer_num']);
        $goods->setElecGoodsAttributes($response['elec_goods_attributes']);
        $goods->setFolt($response['is_folt']);
        $goods->setDeliveryType(DeliveryType::tryFrom($response['delivery_type']));
        $goods->setSecondHand($response['second_hand']);
        $goods->setGoodsProperties($response['goods_property_list']);
        $goods->setVideoGallery($response['video_gallery']);
        $goods->setZhiHuanBuXiu($response['zhi_huan_bu_xiu']);
        $goods->setDeliveryOneDay((bool) $response['delivery_one_day']);
        $goods->setImageUrl($response['image_url']);
        $goods->setOverseaType($response['oversea_type']);
        $goods->setWarmTips($response['warm_tips']);
        $goods->setGoodsDesc($response['goods_desc']);
        $goods->setWarehouse($response['warehouse']);
        $goods->setCarouselGalleryList($response['carousel_gallery_list']);
        $goods->setOutSourceType($response['out_source_type']);
        $goods->setOutSourceGoodsId($response['out_source_goods_id']);
        $goods->setGoodsTravelAttr($response['goods_travel_attr']);
        $goods->setQuanGuoLianBao((bool) $response['quan_guo_lian_bao']);
        $goods->setBadFruitClaim((int) $response['bad_fruit_claim']);
        $goods->setInvoiceStatus((bool) $response['invoice_status']);
        $goods->setGroupPreSale((bool) $response['is_group_pre_sale']);
        $goods->setSkuPreSale((bool) $response['is_sku_pre_sale']);
        $goods->setPreSale((bool) $response['is_pre_sale']);
        $goods->setRefundable((bool) $response['is_refundable']);
        $goods->setLackOfWeightClaim((bool) $response['lack_of_weight_claim']);
        $goods->setMarketPrice($response['market_price']);
        $goods->setOrderLimit($response['order_limit']);
        $goods->setThumbUrl($response['thumb_url']);
        $goods->setDetailGalleryList($response['detail_gallery_list']);
        $goods->setTinyName($response['tiny_name']);
        $goods->setGoodsTradeAttr($response['goods_trade_attr']);
        $goods->setGoodsType(GoodsType::tryFrom($response['goods_type']));
        $goods->setBuyLimit($response['buy_limit']);
        $goods->setOverseaGoods($response['oversea_goods']);

        if ($response['pre_sale_time'] > 0) {
            $goods->setPreSaleTime(Carbon::createFromTimestamp($response['pre_sale_time'], date_default_timezone_get()));
        }

        $category = $this->categoryRepository->find($response['cat_id']);
        $goods->setCategory($category);

        $country = $this->countryRepository->find($response['country_id']);
        $goods->setCountry($country);

        $template = $this->templateRepository->find($response['cost_template_id']);
        $goods->setCostTemplate($template);

        // 主要的商品信息
        $this->entityManager->persist($goods);
        $this->entityManager->flush();

        foreach ($response['sku_list'] as $item) {
            $sku = $this->skuRepository->findOneBy([
                'goods' => $goods,
                'id' => $item['sku_id'],
            ]);
            if ($sku === null) {
                $sku = new Sku();
                $sku->setGoods($goods);
                $sku->setId($item['sku_id']);
            }
            $sku->setOutSkuSn($item['out_sku_sn']);
            $sku->setMultiPrice($item['multi_price']);
            $sku->setThumbUrl($item['thumb_url']);
            $sku->setPreSaleTime($item['sku_pre_sale_time'] !== null && $item['sku_pre_sale_time'] !== 0 ? \DateTimeImmutable::createFromFormat('U', (string) $item['sku_pre_sale_time']) : null);
            $sku->setQuantity($item['quantity']);
            $sku->setReserveQuantity($item['reserve_quantity']);
            $sku->setLength($item['length']);
            $sku->setWeight($item['weight']);
            $sku->setOnsale((bool) $item['is_onsale']);
            $sku->setOverseaSku($item['oversea_sku']);
            $sku->setOutSourceSkuId($item['out_source_sku_id']);
            $sku->setSpecDetails($item['spec']);
            $sku->setPrice($item['price']);
            $sku->setLimitQuantity($item['limit_quantity']);
            $sku->setSkuProperties($item['sku_property_list']);
            $this->entityManager->persist($sku);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
