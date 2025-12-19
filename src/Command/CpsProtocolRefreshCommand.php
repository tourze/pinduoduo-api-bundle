<?php

namespace PinduoduoApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '更新多多进宝状态')]
final class CpsProtocolRefreshCommand extends Command
{
    public const NAME = 'pdd:refresh-cps-protocol-status';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PinduoduoClient $pinduoduoClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('force', InputArgument::OPTIONAL, '是否强制刷新', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $malls = $this->mallRepository->findAll();
        foreach ($malls as $mall) {
            foreach ($mall->getAuthLogs() as $authLog) {
                // 刷新多多进宝状态
                // @see https://open.pinduoduo.com/application/document/api?id=pdd.mall.cps.protocol.status.query
                try {
                    $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.mall.cps.protocol.status.query');
                    // $this->logger->info('授权日志', ['authLog' => $authLog]);
                    // $this->logger->info('协议刷新响应', ['response' => $response]);
                    if (isset($response['status']) && is_bool($response['status'])) {
                        $mall->setCpsProtocolStatus($response['status']);
                        $this->entityManager->persist($mall);
                        $this->entityManager->flush();
                    }
                } catch (\Throwable $exception) {
                    $output->write('更新多多进宝状态失败：' . $exception);
                }
            }
        }

        return Command::SUCCESS;
    }
}
