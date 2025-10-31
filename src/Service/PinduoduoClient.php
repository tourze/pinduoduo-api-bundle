<?php

namespace PinduoduoApiBundle\Service;

use Carbon\CarbonImmutable;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Service\SmartHttpClient;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Exception\InvalidRequestException;
use PinduoduoApiBundle\Exception\PddApiException;
use PinduoduoApiBundle\Exception\UnauthorizedException;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use PinduoduoApiBundle\Request\BasePddRequest;
use PinduoduoApiBundle\Request\RefreshTokenRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\BacktraceHelper\Backtrace;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;

/**
 * 拼多多API请求客户端
 */
#[Autoconfigure(lazy: true, public: true)]
#[WithMonologChannel(channel: 'pinduoduo_api')]
class PinduoduoClient extends ApiClient
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger,
        private readonly AuthLogRepository $authLogRepository,
        private readonly SmartHttpClient $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): SmartHttpClient
    {
        return $this->httpClient;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    public function getLabel(): string
    {
        return '拼多多API请求客户端';
    }

    /**
     * 获取商户的AccessToken
     *
     * @return array<string, mixed>
     */
    public function getAccountAccessToken(Account $account, string $refreshToken = '', bool $refresh = false): array
    {
        $cacheKey = $this->getAccessTokenCacheKey($account);
        if ($refresh) {
            $this->cache->delete($cacheKey);
        }

        /** @var array<string, mixed> $cachedResult */
        $cachedResult = $this->cache->get($cacheKey, function (ItemInterface $item) use ($account, $refreshToken): array {
            $request = new RefreshTokenRequest();
            $clientId = $account->getClientId();
            if (null !== $clientId) {
                $request->setClientId($clientId);
            }
            $request->setClientSecret($account->getClientSecret());
            $request->setRefreshToken($refreshToken);

            $result = $this->request($request);
            if (!is_array($result)) {
                throw new \InvalidArgumentException('获取访问令牌响应格式错误');
            }
            $result['start_time'] = CarbonImmutable::now()->getTimestamp();

            if (!isset($result['expires_in']) || !is_int($result['expires_in'])) {
                throw new \InvalidArgumentException('响应中缺少有效的expires_in字段');
            }
            $item->expiresAfter($result['expires_in'] - 10);

            return $result;
        });

        return $cachedResult;
    }

    private function getAccessTokenCacheKey(Account $account): string
    {
        return "PinduoduoApiBundle_getAccountAccessToken_{$account->getId()}";
    }

    public function request(RequestInterface $request): mixed
    {
        try {
            return parent::request($request);
        } catch (HttpClientException $exception) {
            if ($request instanceof BasePddRequest && str_contains($exception->getMessage(), 'access_token已过期')) {
                $this->logger->warning('AccessToken过期，主动刷新一次', [
                    'request' => $request,
                    'exception' => $exception,
                ]);

                // 刷新AccessToken
                $this->cache->delete($this->getAccessTokenCacheKey($request->getAccount()));
                $tokenData = $this->getAccountAccessToken($request->getAccount(), '', true);
                if (isset($tokenData['access_token']) && is_string($tokenData['access_token'])) {
                    $request->setAccessToken($tokenData['access_token']);
                } else {
                    throw new \InvalidArgumentException('获取访问令牌失败');
                }

                return parent::request($request);
            }

            throw $exception;
        }
    }

    /**
     * 通过店铺发起请求
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function requestByMall(Mall $mall, string $api, array $params = []): array
    {
        /** @var AuthLog|null $authLog */
        $authLog = $this->authLogRepository->createQueryBuilder('a')
            ->where('a.mall=:mall AND a.scope LIKE :api')
            ->setParameter('mall', $mall)
            ->setParameter('api', "%{$api}%")
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $authLog) {
            throw new UnauthorizedException("未授权调用：{$api}");
        }

        $account = $authLog->getAccount();
        $accessToken = $authLog->getAccessToken();

        if (null === $account || null === $accessToken) {
            throw new UnauthorizedException("授权信息不完整：{$api}");
        }

        $request = new BasePddRequest();
        $request->setAccount($account);
        $request->setAccessToken($accessToken);
        $request->setType($api);
        $request->setParams($params);

        $result = $this->request($request);
        if (!is_array($result)) {
            throw new \InvalidArgumentException('API响应格式错误');
        }

        /** @var array<string, mixed> $validResult */
        $validResult = [];
        foreach ($result as $key => $value) {
            $validResult[(string) $key] = $value;
        }

        return $validResult;
    }

    /**
     * 生成授权地址
     */
    public function generateAuthUrl(Account $account, ?string $state = null): string
    {
        $redirectUri = $this->urlGenerator->generate(
            'pinduoduo-auth-callback',
            ['id' => $account->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $params = [
            'response_type' => 'code',
            'client_id' => $account->getClientId(),
            'redirect_uri' => $redirectUri,
            'state' => $state ?? uniqid('pdd_'),
        ];

        return 'https://mai.pinduoduo.com/h5-login.html?' . http_build_query($params);
    }

    /**
     * 通过授权码换取AccessToken
     *
     * @return array<string, mixed>
     */
    public function getAccessTokenByCode(Account $account, string $code): array
    {
        $request = new BasePddRequest();
        $request->setAccount($account);
        $request->setType('pdd.pop.auth.token.create');
        $request->setParams([
            'code' => $code,
            'client_id' => $account->getClientId(),
            'client_secret' => $account->getClientSecret(),
        ]);

        $result = $this->request($request);
        if (!is_array($result)) {
            throw new \InvalidArgumentException('API响应格式错误');
        }

        /** @var array<string, mixed> $validResult */
        $validResult = [];
        foreach ($result as $key => $value) {
            $validResult[(string) $key] = $value;
        }

        return $validResult;
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        return $this->getBaseUrl() . '/api/router';
    }

    public function getBaseUrl(): string
    {
        return 'https://gw-api.pinduoduo.com';
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        if (!$request instanceof BasePddRequest) {
            throw new InvalidRequestException('Request must be instance of BasePddRequest');
        }

        $params = $request->getParams();
        $params['client_id'] = $request->getAccount()->getClientId();
        $params['type'] = $request->getType();
        $params['timestamp'] = (string) time();
        $params['data_type'] = 'JSON';
        $params['version'] = 'V1';

        if (null !== $request->getAccessToken() && '' !== $request->getAccessToken()) {
            $params['access_token'] = $request->getAccessToken();
        }

        // 生成签名
        $params['sign'] = $this->generateSign($params, $request->getAccount()->getClientSecret());

        return [
            'body' => http_build_query($params),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    /**
     * 生成签名
     *
     * @param array<string, mixed> $params
     */
    private function generateSign(array $params, string $secret): string
    {
        ksort($params);
        $str = $secret;
        foreach ($params as $key => $value) {
            if (!is_array($value)) {
                $valueStr = match (true) {
                    is_string($value) => $value,
                    is_int($value), is_float($value) => (string) $value,
                    is_bool($value) => $value ? '1' : '0',
                    default => '',
                };
                $str .= $key . $valueStr;
            }
        }
        $str .= $secret;

        return strtoupper(md5($str));
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $json = json_decode($response->getContent(), true);

        if (!is_array($json)) {
            return $json;
        }

        if (isset($json['error_response'])) {
            /** @var array<string, mixed> $errorResponse */
            $errorResponse = $json['error_response'];
            $this->logger->error('拼多多API返回错误', [
                'request' => $request,
                'error' => $errorResponse,
                'backtrace' => Backtrace::create()->toString(),
            ]);

            throw new PddApiException($errorResponse);
        }

        // 提取真正的响应数据
        foreach ($json as $key => $value) {
            if (is_string($key) && str_ends_with($key, '_response')) {
                return $value;
            }
        }

        return $json;
    }
}
