<?php

namespace PinduoduoApiBundle\Command\Order;

use Carbon\CarbonImmutable;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * ①. 一次请求只能查询时间跨度为30分钟的增量交易记录，即end_updated_at - start_updated_at<= 30min。
 * ②. 通过从后往前翻页的方式以及结束时间不小于拼多多系统时间前3min可以避免漏单问题。
 */
#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '获取增量订单列表')]
class OrderIncrementListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-increment-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('mallId') !== null) {
            $malls = $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        foreach ($malls as $mall) {
            $now = CarbonImmutable::now();
            $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::打单);

            $page = 1;
            $pageSize = 100;

            do {
                $result = $sdk->auth_api->request('pdd.order.number.list.increment.get', [
                    'page' => $page,
                    'page_size' => $pageSize,
                    'start_updated_at' => $now->subMinutes(29)->getTimestamp(),
                    'end_updated_at' => $now->getTimestamp(),
                    'is_lucky_flag' => 0, // 订单类型（是否抽奖订单），0-全部，1-非抽奖订单，2-抽奖订单
                    'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                    'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
                ]);
                if (!isset($result['order_sn_increment_get_response'])) {
                    break;
                }
                $result = $result['order_sn_increment_get_response'];

                $hasNext = $result['has_next'];
                foreach ($result['order_list'] as $item) {
                    $message = new SyncOrderListDetailMessage();
                    $message->setMallId($mall->getId());
                    $message->setOrderInfo($item);
                    $this->messageBus->dispatch($message);
                }
            } while ($hasNext);
        }

        return Command::SUCCESS;
    }
}
