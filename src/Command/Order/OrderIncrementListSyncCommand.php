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

/**
 * ①. 一次请求只能查询时间跨度为30分钟的增量交易记录，即end_updated_at - start_updated_at<= 30min。
 * ②. 通过从后往前翻页的方式以及结束时间不小于拼多多系统时间前3min可以避免漏单问题。
 */
#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '获取增量订单列表')]
final class OrderIncrementListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-increment-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
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
        $malls = $this->getMallsToProcess($input);

        foreach ($malls as $mall) {
            $this->syncIncrementOrdersForMall($mall);
        }

        return Command::SUCCESS;
    }

    /**
     * @return list<Mall>
     */
    private function getMallsToProcess(InputInterface $input): array
    {
        if (null !== $input->getArgument('mallId')) {
            return $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        }

        return $this->mallRepository->findAll();
    }

    private function syncIncrementOrdersForMall(Mall $mall): void
    {
        // 直接使用 PinduoduoClient 调用 API

        $now = CarbonImmutable::now();
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->fetchIncrementOrdersPage($mall, $now, $page, $pageSize);
            if (null === $result) {
                break;
            }

            $hasNext = $result['has_next'] ?? false;
            $orderList = $result['order_list'] ?? [];
            if (\is_array($orderList)) {
                $this->dispatchOrderMessages($mall, $orderList);
            }
            ++$page;
        } while ($hasNext);
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchIncrementOrdersPage(Mall $mall, CarbonImmutable $now, int $page, int $pageSize): ?array
    {
        try {
            return $this->pinduoduoClient->requestByMall($mall, 'pdd.order.number.list.increment.get', [
                'page' => $page,
                'page_size' => $pageSize,
                'start_updated_at' => $now->subMinutes(29)->getTimestamp(),
                'end_updated_at' => $now->getTimestamp(),
                'is_lucky_flag' => 0, // 订单类型（是否抽奖订单），0-全部，1-非抽奖订单，2-抽奖订单
                'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array<mixed> $orderList
     */
    private function dispatchOrderMessages(Mall $mall, array $orderList): void
    {
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
    }
}
