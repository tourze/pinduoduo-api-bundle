<?php

namespace PinduoduoApiBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '*/5 * * * *')]
#[AsCommand(name: self::NAME, description: '更新AccessToken')]
#[WithMonologChannel(channel: 'pinduoduo_api')]
class AccessTokenRefreshCommand extends Command
{
    public const NAME = 'pdd:refresh-access-token';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly PinduoduoClient $pinduoduoClient,
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
            $this->refreshMallAuthTokens($mall, $now, $force);
        }

        return Command::SUCCESS;
    }

    private function refreshMallAuthTokens(Mall $mall, CarbonImmutable $now, bool $force): void
    {
        foreach ($mall->getAuthLogs() as $authLog) {
            if ($this->shouldSkipTokenRefresh($authLog, $now, $force)) {
                continue;
            }

            $this->refreshAuthLogToken($mall, $authLog);
        }
    }

    private function shouldSkipTokenRefresh(AuthLog $authLog, CarbonImmutable $now, bool $force): bool
    {
        return !$force && $now->diffInMinutes($authLog->getTokenExpireTime()) > 60;
    }

    private function refreshAuthLogToken(Mall $mall, AuthLog $authLog): void
    {
        try {
            $account = $authLog->getAccount();
            $refreshToken = $authLog->getRefreshToken();
            if (null === $account || null === $refreshToken) {
                return;
            }

            $token = $this->pinduoduoClient->getAccountAccessToken($account, $refreshToken, true);
            // $this->logger->info('访问令牌', ['token' => $token]);

            if (!$this->isValidTokenResponse($token)) {
                $this->logTokenError($token, $mall, $authLog);

                return;
            }

            $this->updateAuthLogWithToken($authLog, $token);
            $this->entityManager->persist($authLog);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logRefreshError($e, $mall, $authLog);
        }
    }

    /**
     * @param array<mixed> $token
     */
    private function isValidTokenResponse(array $token): bool
    {
        return isset($token['access_token']);
    }

    /**
     * @param array<mixed> $token
     */
    private function logTokenError(array $token, Mall $mall, AuthLog $authLog): void
    {
        $this->logger->error('刷新token时找不到合适结果', [
            'token' => $token,
            'mall' => $mall,
            'authLog' => $authLog,
        ]);
    }

    private function logRefreshError(\Exception $e, Mall $mall, AuthLog $authLog): void
    {
        $this->logger->error('刷新token失败', [
            'error' => $e->getMessage(),
            'mall' => $mall,
            'authLog' => $authLog,
        ]);
    }

    /**
     * @param array<mixed> $token
     */
    private function updateAuthLogWithToken(AuthLog $authLog, array $token): void
    {
        $authLog->setAccessToken(is_string($token['access_token'] ?? null) ? $token['access_token'] : null);
        $authLog->setRefreshToken(is_string($token['refresh_token'] ?? null) ? $token['refresh_token'] : null);
        $scope = $token['scope'] ?? null;
        if (is_array($scope)) {
            /** @var array<int, string> $stringScope */
            $stringScope = array_map(static fn (mixed $value): string => is_scalar($value) ? (string) $value : '', $scope);
            $authLog->setScope($stringScope);
        } else {
            $authLog->setScope(null);
        }

        $startTime = is_numeric($token['start_time'] ?? null) ? (int) $token['start_time'] : 0;
        $expiresIn = is_numeric($token['expires_in'] ?? null) ? (int) $token['expires_in'] : 0;
        $authLog->setTokenExpireTime(
            CarbonImmutable::createFromTimestamp(
                $startTime + $expiresIn,
                date_default_timezone_get()
            )
        );

        // Convert mixed array to array<string, mixed>
        $context = [];
        foreach ($token as $key => $value) {
            $context[(string) $key] = $value;
        }
        $authLog->setContext($context);
    }
}
