<?php

namespace PinduoduoApiBundle\Command\Order;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use PinduoduoApiBundle\Enum\Order\GroupStatus;
use PinduoduoApiBundle\Enum\Order\MktBizType;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Enum\Order\PayType;
use PinduoduoApiBundle\Enum\Order\RefundStatus;
use PinduoduoApiBundle\Enum\Order\RiskControlStatus;
use PinduoduoApiBundle\Enum\Order\ShippingType;
use PinduoduoApiBundle\Enum\Order\StockOutHandleStatus;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Yiisoft\Json\Json;

#[AsCronTask('45 */6 * * *')]
#[AsCommand(name: OrderFullListSyncCommand::NAME, description: '获取全量订单列表')]
class OrderFullListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-full-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $categoryRepository,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('获取全量订单列表')
            ->addArgument('mallId', InputArgument::OPTIONAL)
            ->addArgument('date', InputArgument::OPTIONAL);
    }

    private function syncList(Mall $mall, InputInterface $input, OutputInterface $output): void
    {
        $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::打单);
        if ($sdk === null) {
            $output->writeln('找不到打单sdk授权');

            return;
        }

        if ($input->getArgument('date') !== null) {
            $endTime = CarbonImmutable::parse($input->getArgument('date'))->endOfDay();
            $startTime = CarbonImmutable::parse($input->getArgument('date'))->startOfDay();
        } else {
            $endTime = CarbonImmutable::now();
            $startTime = $endTime->subDays(90);
        }
        $period = CarbonPeriod::between($startTime, $endTime);
        foreach ($period->toArray() as $date) {
            $page = 1;
            $pageSize = 100;

            do {
                // 打单、进销存、虚拟商家后台系统、企业ERP、商家后台系统、订单处理、电子凭证商家后台系统、跨境企业ERP报关版
                $result = $sdk->auth_api->request('pdd.order.list.get', [
                    'page' => $page,
                    'page_size' => $pageSize,
                    'start_confirm_at' => $date->startOfDay()->getTimestamp(),
                    'end_confirm_at' => $date->endOfDay()->getTimestamp(),
                    'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                    'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
                ]);
                $output->writeln("[{$date->toDateTimeString()}] -> {$page}: " . Json::encode($result));
                if (!isset($result['order_list_get_response'])) {
                    $output->writeln('订单读取出错:' . Json::encode($result));
                    break;
                }
                $result = $result['order_list_get_response'];

                $hasNext = $result['has_next'];
                foreach ($result['order_list'] as $item) {
                    $this->logger->info('处理订单行数据', [
                        'item' => $item,
                    ]);
                    $order = $this->orderRepository->findOneBy(['orderSn' => $item['order_sn']]);
                    if ($order === null) {
                        $order = new Order();
                        $order->setOrderSn($item['order_sn']);
                    }

                    // 保存请求时的上下文
                    $context = $order->getContext() ?: [];
                    $context['pdd.order.list.get'] = $item;
                    $order->setContext($context);

                    $order->setSupportNationwideWarranty((bool) $item['support_nationwide_warranty']);
                    $order->setFreeSf((bool) $item['free_sf']);
                    $order->setReturnFreightPayer((bool) $item['return_freight_payer']);
                    $order->setDeliveryOneDay((bool) $item['delivery_one_day']);
                    $order->setStockOut((bool) $item['is_stock_out']);
                    $order->setLuckyFlag((bool) $item['is_lucky_flag']);
                    $order->setInvoiceStatus((bool) $item['invoice_status']);
                    $order->setOnlySupportReplace((bool) $item['only_support_replace']);
                    $order->setDuoduoWholesale((bool) $item['duoduo_wholesale']);
                    $order->setPreSale((bool) $item['is_pre_sale']);

                    $order->setGroupStatus(GroupStatus::tryFrom($item['group_status']));
                    $order->setOrderStatus(OrderStatus::tryFrom($item['order_status']));
                    $order->setRiskControlStatus(RiskControlStatus::tryFrom($item['risk_control_status']));
                    $order->setRefundStatus(RefundStatus::tryFrom($item['refund_status']));
                    $order->setMktBizType(MktBizType::tryFrom($item['mkt_biz_type']));
                    $order->setShippingType(ShippingType::tryFrom($item['shipping_type'])?->value);
                    $order->setPayType(PayType::tryFrom($item['pay_type']));
                    $order->setConfirmStatus(ConfirmStatus::tryFrom($item['confirm_status']));
                    $order->setStockOutHandleStatus(StockOutHandleStatus::tryFrom($item['stock_out_handle_status']));

                    $order->setItemList($item['item_list']);
                    $order->setDiscountAmount($item['discount_amount']);
                    $order->setPlatformDiscount($item['platform_discount']);
                    $order->setCardInfoList($item['card_info_list']);
                    $order->setGiftList($item['gift_list']);
                    $order->setServiceFeeDetail($item['service_fee_detail']);
                    $order->setCapitalFreeDiscount($item['capital_free_discount']);
                    $order->setOrderTagList($item['order_tag_list']);
                    $order->setRemark($item['remark']);
                    $order->setOrderChangeAmount($item['order_change_amount']);
                    $order->setTrackingNumber($item['tracking_number']);
                    $order->setBuyerMemo($item['buyer_memo']);
                    $order->setGoodsAmount($item['goods_amount']);
                    $order->setPayAmount($item['pay_amount']);
                    $order->setSellerDiscount($item['seller_discount']);
                    $order->setPostage($item['postage']);

                    $order->setCreateTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['created_time']) ?: null);
                    $order->setLastShipTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['last_ship_time']) ?: null);
                    $order->setReceiveTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['receive_time']) ?: null);
                    $order->setPayTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['pay_time']) ?: null);
                    $order->setUpdateTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['updated_at']) ?: null);
                    $order->setShippingTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['shipping_time']) ?: null);
                    $order->setConfirmTime(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['confirm_time']) ?: null);

                    // 订单也有分类，一般我们只拿最下面的分类即可
                    $category = null;
                    $checkKeys = [
                        'cat_id_4',
                        'cat_id_3',
                        'cat_id_2',
                        'cat_id_1',
                    ];
                    foreach ($checkKeys as $key) {
                        if ($category !== null) {
                            break;
                        }
                        if (isset($item[$key]) && $item[$key] !== null && $item[$key] !== 0) {
                            $category = $this->categoryRepository->find($item['cat_id_4']);
                        }
                    }
                    $order->setCategory($category);

                    $this->entityManager->persist($order);
                    $this->entityManager->flush();
                }
            } while ($hasNext);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('mallId') !== null) {
            $malls = $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        foreach ($malls as $mall) {
            $this->syncList($mall, $input, $output);
        }

        return Command::SUCCESS;
    }
}
