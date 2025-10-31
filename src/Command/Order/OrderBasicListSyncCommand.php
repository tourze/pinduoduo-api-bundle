<?php

namespace PinduoduoApiBundle\Command\Order;

use Carbon\CarbonImmutable;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* 5 * * *')]
#[AsCommand(name: self::NAME, description: '订单基础信息列表查询接口（根据成交时间）')]
class OrderBasicListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-basic-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('获取全量订单列表')
            ->addArgument('mallId', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now();

        $mall = $this->mallRepository->find($input->getArgument('mallId'));
        if (null === $mall) {
            $output->writeln('找不到指定的店铺');

            return Command::FAILURE;
        }

        $page = 1;
        $pageSize = 100;

        $hasNext = false;
        do {
            try {
                $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.order.basic.list.get', [
                    'start_confirm_at' => $now->subDays(365)->getTimestamp(),
                    'end_confirm_at' => $now->getTimestamp(),
                    'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                    'page' => $page,
                    'page_size' => $pageSize,
                    'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
                ]);
            } catch (\Exception $e) {
                $output->writeln('订单读取出错:' . $e->getMessage());
                break;
            }

            $hasNext = $result['has_next'] ?? false;
            $orderList = $result['order_list'] ?? [];
            assert(is_array($orderList));

            foreach ($orderList as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $message = new SyncOrderListDetailMessage();
                $mallId = $mall->getId();
                if (null !== $mallId) {
                    $message->setMallId($mallId);
                }
                $message->setOrderInfo($item);
                $this->messageBus->dispatch($message);
            }
            ++$page;
        } while ($hasNext);

        return Command::SUCCESS;
    }
}
