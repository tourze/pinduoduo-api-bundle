<?php

namespace PinduoduoApiBundle\Command\Order;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use PinduoduoApiBundle\Service\OrderDataMapper;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Yiisoft\Json\Json;

#[AsCronTask(expression: '45 */6 * * *')]
#[AsCommand(name: self::NAME, description: '获取全量订单列表')]
#[WithMonologChannel(channel: 'pinduoduo_api')]
final class OrderFullListSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-full-order-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderDataMapper $orderDataMapper,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('获取全量订单列表')
            ->addArgument('mallId', InputArgument::OPTIONAL)
            ->addArgument('date', InputArgument::OPTIONAL)
        ;
    }

    private function syncList(Mall $mall, InputInterface $input, OutputInterface $output): void
    {
        $timeRange = $this->getTimeRange($input);
        $period = CarbonPeriod::between($timeRange['start'], $timeRange['end']);

        foreach ($period->toArray() as $date) {
            $this->syncOrdersForDate($mall, CarbonImmutable::parse($date), $output);
        }
    }

    /**
     * @return array{start: CarbonImmutable, end: CarbonImmutable}
     */
    private function getTimeRange(InputInterface $input): array
    {
        $dateArg = $input->getArgument('date');
        if (null !== $dateArg && is_string($dateArg)) {
            $endTime = CarbonImmutable::parse($dateArg)->endOfDay();
            $startTime = CarbonImmutable::parse($dateArg)->startOfDay();
        } else {
            $endTime = CarbonImmutable::now();
            $startTime = $endTime->subDays(90);
        }

        return ['start' => $startTime, 'end' => $endTime];
    }

    private function syncOrdersForDate(Mall $mall, CarbonImmutable $date, OutputInterface $output): void
    {
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->fetchOrdersPage($mall, $date, $page, $pageSize, $output);
            if (null === $result) {
                break;
            }

            $this->processOrderListFromResult($result);
            $hasNext = $result['has_next'] ?? false;
            ++$page;
        } while ($hasNext);
    }

    /**
     * @param array<mixed> $result
     */
    private function processOrderListFromResult(array $result): void
    {
        $orderList = $result['order_list'] ?? [];
        if (!is_array($orderList)) {
            return;
        }

        foreach ($orderList as $item) {
            if (is_array($item)) {
                $this->processOrderItem($item);
            }
        }
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchOrdersPage(Mall $mall, CarbonImmutable $date, int $page, int $pageSize, OutputInterface $output): ?array
    {
        try {
            $result = $this->pinduoduoClient->requestByMall($mall, 'pdd.order.list.get', [
                'page' => $page,
                'page_size' => $pageSize,
                'start_confirm_at' => $date->startOfDay()->getTimestamp(),
                'end_confirm_at' => $date->endOfDay()->getTimestamp(),
                'order_status' => 5, // 发货状态，1：待发货，2：已发货待签收，3：已签收 5：全部
                'refund_status' => 5, // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
            ]);

            $output->writeln("[{$date->toDateTimeString()}] -> {$page}: " . Json::encode($result));

            return $result;
        } catch (\Exception $e) {
            $output->writeln('订单读取出错:' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function processOrderItem(array $item): void
    {
        $this->logger->info('处理订单行数据', ['item' => $item]);

        $orderSn = $item['order_sn'] ?? null;
        if (!is_string($orderSn)) {
            return;
        }

        $order = $this->orderRepository->findOneBy(['orderSn' => $orderSn]);
        if (null === $order) {
            $order = new Order();
            $order->setOrderSn($orderSn);
        }

        $this->orderDataMapper->mapToOrder($order, $item);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null !== $input->getArgument('mallId')) {
            $malls = $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        /** @var Mall $mall */
        foreach ($malls as $mall) {
            $this->syncList($mall, $input, $output);
        }

        return Command::SUCCESS;
    }
}
