<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Service;

use HttpClientBundle\Client\ApiClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(PinduoduoClient::class)]
#[RunTestsInSeparateProcesses]
final class PinduoduoClientTest extends AbstractIntegrationTestCase
{
    private PinduoduoClient $client;

    protected function onSetUp(): void
    {
        $this->client = self::getService(PinduoduoClient::class);
    }

    public function testExtendsApiClient(): void
    {
        $this->assertInstanceOf(ApiClient::class, $this->client);
    }

    public function testGetLabel(): void
    {
        $label = $this->client->getLabel();

        $this->assertEquals('拼多多API请求客户端', $label);
    }

    public function testGetBaseUrl(): void
    {
        $baseUrl = $this->client->getBaseUrl();

        $this->assertEquals('https://gw-api.pinduoduo.com', $baseUrl);
    }

    public function testClientCanBeInstantiated(): void
    {
        $this->assertInstanceOf(PinduoduoClient::class, $this->client);
    }

    public function testClientHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass($this->client);

        $this->assertTrue($reflection->hasMethod('request'));
        $this->assertTrue($reflection->hasMethod('requestByMall'));
        $this->assertTrue($reflection->hasMethod('generateAuthUrl'));
        $this->assertTrue($reflection->hasMethod('getAccessTokenCacheKey'));
    }

    public function testClientHasCorrectDependencies(): void
    {
        $this->assertInstanceOf(PinduoduoClient::class, $this->client);

        $this->assertNotEmpty($this->client->getLabel());
        $this->assertNotEmpty($this->client->getBaseUrl());
    }

    public function testGenerateAuthUrl(): void
    {
        // 创建一个真实的 Account 对象
        $account = new Account();
        $account->setTitle('Test Account');
        $account->setClientId('test_client_id');
        $account->setClientSecret('test_client_secret');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        // 测试生成授权URL的功能
        $url = $this->client->generateAuthUrl($account);

        $this->assertNotEmpty($url);
        $this->assertStringStartsWith('https://', $url);
    }

    public function testRequest(): void
    {
        // 基本的测试，确保方法可以被调用（即使没有有效的token）
        self::markTestSkipped('PinduoduoClient::request() 需要有效的API凭证进行完整测试');
    }

    public function testRequestByMall(): void
    {
        // 基本的测试，确保方法可以被调用
        self::markTestSkipped('PinduoduoClient::requestByMall() 需要有效的API凭证和商铺数据进行完整测试');
    }
}
