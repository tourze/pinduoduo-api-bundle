<?php

namespace PinduoduoApiBundle\Command\Order;

use Carbon\CarbonImmutable;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Exception\SdkNotFoundException;
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
use Yiisoft\Json\Json;

#[AsCronTask(expression: '* 5 * * *')]
#[AsCommand(name: self::NAME, description: '订单基础信息列表查询接口（根据成交时间）')]
class OrderBasicListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-basic-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('获取全量订单列表')
            ->addArgument('mallId', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now();

        $mall = $this->mallRepository->find($input->getArgument('mallId'));
        if ($mall === null) {
            $output->writeln('找不到指定的店铺');

            return Command::FAILURE;
        }

        $sdk = $this->sdkService->getMallSdk($mall, ApplicationType::打单);
        if ($sdk === null) {
            throw new SdkNotFoundException('找不到打单sdk授权');
        }

        $page = 1;
        $pageSize = 100;

        $hasNext = false;
        do {
            // 推广优化、进销存、商品优化分析、搬家上货
            $result = $sdk->auth_api->request('pdd.order.basic.list.get', [
                'start_confirm_at' => $now->subDays(365)->getTimestamp(),
                'end_confirm_at' => $now->getTimestamp(),
                'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                'page' => $page,
                'page_size' => $pageSize,
                'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
            ]);
            if (!isset($result['order_basic_list_get_response'])) {
                $output->writeln('订单读取出错:' . Json::encode($result));
                break;
            }
            $result = $result['order_basic_list_get_response'];

            $hasNext = $result['has_next'];
            foreach ($result['order_list'] as $item) {
                $message = new SyncOrderListDetailMessage();
                $message->setMallId($mall->getId());
                $message->setOrderInfo($item);
                $this->messageBus->dispatch($message);
            }
        } while ($hasNext);

        return Command::SUCCESS;
    }
}
