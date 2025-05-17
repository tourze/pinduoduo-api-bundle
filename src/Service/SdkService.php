<?php

namespace PinduoduoApiBundle\Service;

use Doctrine\Common\Collections\Criteria;
use Hanson\Foundation\Log;
use Justmd5\PinDuoDuo\PinDuoDuo;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Exception\PddApiException;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SdkService
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly KernelInterface $kernel,
        private readonly LoggerInterface $logger,
        private readonly AuthLogRepository $authLogRepository,
    ) {
    }

    public function getMerchantSdk(Account $account): PinDuoDuo
    {
        Log::setLogger($this->logger);

        $config = [
            'client_id' => $account->getClientId(),
            'client_secret' => $account->getClientSecret(),
            'debug' => true,
            'member_type' => 'MERCHANT', // 用户角色 ：MERCHANT(商家授权),H5(移动端),多多进宝推手(JINBAO),快团团团长(KTT),拼多多电子面单用户(LOGISTICS)
            'redirect_uri' => $this->urlGenerator->generate('pinduoduo-auth-callback', ['id' => $account->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'log' => [
                'name' => 'pinduoduo',
                'file' => $this->kernel->getLogDir() . "/{$account->getId()}.log",
                'level' => 'debug',
                'permission' => 0o777,
            ],
        ];

        return new PinDuoDuo($config);
    }

    /**
     * 获取店铺指定类型的SDK实例
     */
    public function getMallSdk(Mall $mall, ApplicationType $applicationType): ?PinDuoDuo
    {
        $account = null;
        $accessToken = null;
        foreach ($mall->getAuthLogs() as $authLog) {
            if ($authLog->getAccount()->getApplicationType() === $applicationType) {
                $account = $authLog->getAccount();
                $accessToken = $authLog->getAccessToken();
                break;
            }
        }
        if (!$account) {
            return null;
        }

        $sdk = $this->getMerchantSdk($account);

        return $sdk->oauth->createAuthorization($accessToken);
    }

    public function request(Mall $mall, string $api, array $params = []): array
    {
        $authLog = $this->authLogRepository->createQueryBuilder('a')
            ->where('a.mall=:mall AND a.scope LIKE :api')
            ->setParameter('mall', $mall)
            ->setParameter('api', "%{$api}%")
            ->orderBy('a.id', Criteria::DESC)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$authLog) {
            throw new \RuntimeException("未授权调用：{$api}");
        }
        /** @var AuthLog $authLog */
        $sdk = $this->getMerchantSdk($authLog->getAccount());
        $sdk = $sdk->oauth->createAuthorization($authLog->getAccessToken());

        $result = $sdk->auth_api->request($api, $params);
        $this->logger->info('发起PDD请求，并获得结果', [
            'mall' => $mall,
            'api' => $api,
            'params' => $params,
            // 'result' => $result,
        ]);
        if (isset($result['error_response'])) {
            throw new PddApiException($result['error_response']);
        }

        return $result;
    }
}
