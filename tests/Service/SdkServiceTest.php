<?php

namespace PinduoduoApiBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Justmd5\PinDuoDuo\PinDuoDuo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Exception\UnauthorizedException;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SdkServiceTest extends TestCase
{
    private SdkService $sdkService;
    private MockObject|UrlGeneratorInterface $urlGenerator;
    private MockObject|KernelInterface $kernel;
    private MockObject|LoggerInterface $logger;
    private MockObject|AuthLogRepository $authLogRepository;
    
    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->authLogRepository = $this->createMock(AuthLogRepository::class);
        
        $this->sdkService = new SdkService(
            $this->urlGenerator,
            $this->kernel,
            $this->logger,
            $this->authLogRepository
        );
    }
    
    public function testGetMerchantSdk_withValidAccount_returnsPinDuoDuoInstance(): void
    {
        $account = $this->createAccountMock('123', 'test_client_id', 'test_client_secret');
        
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('pinduoduo-auth-callback', ['id' => '123'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/auth/callback/123');
            
        $this->kernel->expects($this->once())
            ->method('getLogDir')
            ->willReturn('/var/log');
            
        $sdk = $this->sdkService->getMerchantSdk($account);
        
        $this->assertInstanceOf(PinDuoDuo::class, $sdk);
    }
    
    public function testGetMallSdk_withValidMallAndAppType_returnsPinDuoDuoInstance(): void
    {
        $applicationType = ApplicationType::企业ERP;
        
        $account = $this->createAccountMock('123', 'test_client_id', 'test_client_secret', $applicationType);
        
        $authLog = $this->createMock(AuthLog::class);
        $authLog->method('getAccount')->willReturn($account);
        $authLog->method('getAccessToken')->willReturn('test_access_token');
        
        $authLogsCollection = new ArrayCollection([$authLog]);
        
        $mall = $this->createMock(Mall::class);
        $mall->method('getAuthLogs')->willReturn($authLogsCollection);
        
        // 由于我们不能直接操作PinDuoDuo实例，这部分只能确认函数流程正确，不做深入断言
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn('https://example.com/auth/callback/123');
            
        $this->kernel->expects($this->once())
            ->method('getLogDir')
            ->willReturn('/var/log');
            
        $sdk = $this->sdkService->getMallSdk($mall, $applicationType);
        
        $this->assertNotNull($sdk);
    }
    
    public function testGetMallSdk_withNoMatchingAccount_returnsNull(): void
    {
        $mall = $this->createMock(Mall::class);
        $mall->method('getAuthLogs')->willReturn(new ArrayCollection([]));
        
        $result = $this->sdkService->getMallSdk($mall, ApplicationType::企业ERP);
        
        $this->assertNull($result);
    }
    
    
    public function testRequest_whenAuthLogNotFound_throwsException(): void
    {
        $mall = $this->createMock(Mall::class);
        
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('orderBy')->willReturnSelf();
        $queryBuilder->method('setMaxResults')->willReturnSelf();
        
        $query = $this->createMock(Query::class);
        $query->method('getOneOrNullResult')->willReturn(null);
        
        $queryBuilder->method('getQuery')->willReturn($query);
        
        $this->authLogRepository->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $apiName = 'pdd.goods.list.get';
        
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage("未授权调用：{$apiName}");
        
        $this->sdkService->request($mall, $apiName);
    }
    
    private function createAccountMock(string $id, string $clientId, string $clientSecret, ?ApplicationType $applicationType = null): Account
    {
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn($id);
        $account->method('getClientId')->willReturn($clientId);
        $account->method('getClientSecret')->willReturn($clientSecret);
        $account->method('getApplicationType')->willReturn($applicationType ?? ApplicationType::企业ERP);
        
        return $account;
    }
} 