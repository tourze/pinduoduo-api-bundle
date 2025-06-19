<?php

namespace PinduoduoApiBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('*/5 * * * *')]
#[AsCommand(name: AccessTokenRefreshCommand::NAME, description: '更新AccessToken')]
class AccessTokenRefreshCommand extends Command
{
    public const NAME = 'pdd:refresh-access-token';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly SdkService $sdkService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('force', InputArgument::OPTIONAL, '是否强制刷新', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now();

        $force = (bool) $input->getArgument('force');

        $malls = $this->mallRepository->findAll();
        foreach ($malls as $mall) {
            foreach ($mall->getAuthLogs() as $authLog) {
                $sdk = $this->sdkService->getMerchantSdk($authLog->getAccount());

                // 快过期了，我们才去刷新
                if (!$force && $now->diffInMinutes($authLog->getTokenExpireTime()) > 60) {
                    continue;
                }
                $token = $sdk->pre_auth->refreshToken($authLog->getRefreshToken());
                dump($token);
                if (!isset($token['access_token'])) {
                    $this->logger->error('刷新token时找不到合适结果', [
                        'token' => $token,
                        'mall' => $mall,
                        'authLog' => $authLog,
                    ]);
                    continue;
                }
                $authLog->setAccessToken($token['access_token']);
                $authLog->setRefreshToken($token['refresh_token']);
                $authLog->setScope($token['scope'] ?? null);
                $authLog->setTokenExpireTime(CarbonImmutable::createFromTimestamp($token['expires_at'], date_default_timezone_get()));
                $authLog->setContext($token);
                $this->entityManager->persist($authLog);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
