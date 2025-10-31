<?php

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
    protected function onSetUp(): void
    {
    }

    public function testExtendsApiClient(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $this->assertInstanceOf(ApiClient::class, $client);
    }

    public function testGetLabel(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $label = $client->getLabel();

        $this->assertEquals('拼多多API请求客户端', $label);
    }

    public function testGetBaseUrl(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $baseUrl = $client->getBaseUrl();

        $this->assertEquals('https://gw-api.pinduoduo.com', $baseUrl);
    }

    public function testClientCanBeInstantiated(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $this->assertInstanceOf(PinduoduoClient::class, $client);
    }

    public function testClientHasRequiredMethods(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $reflection = new \ReflectionClass($client);

        $this->assertTrue($reflection->hasMethod('request'));
        $this->assertTrue($reflection->hasMethod('requestByMall'));
        $this->assertTrue($reflection->hasMethod('generateAuthUrl'));
        $this->assertTrue($reflection->hasMethod('getAccessTokenCacheKey'));
    }

    public function testClientHasCorrectDependencies(): void
    {
        $client = self::getService(PinduoduoClient::class);
        $this->assertInstanceOf(PinduoduoClient::class, $client);

        $this->assertNotEmpty($client->getLabel());
        $this->assertNotEmpty($client->getBaseUrl());
    }

    public function testGenerateAuthUrl(): void
    {
        $client = self::getService(PinduoduoClient::class);

        // 创建一个模拟的Account对象
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn('1');

        // 测试生成授权URL的功能
        $url = $client->generateAuthUrl($account);

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
