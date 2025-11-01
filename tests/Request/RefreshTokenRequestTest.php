<?php

namespace PinduoduoApiBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Request\RefreshTokenRequest;

/**
 * @internal
 */
#[CoversClass(RefreshTokenRequest::class)]
final class RefreshTokenRequestTest extends RequestTestCase
{
    private RefreshTokenRequest $request;

    protected function setUp(): void
    {
        $this->request = new RefreshTokenRequest();
    }

    public function testImplementsRequestInterface(): void
    {
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    public function testClientIdGetterSetter(): void
    {
        $clientId = 'test_client_id_123';
        $this->request->setClientId($clientId);

        $this->assertEquals($clientId, $this->request->getClientId());
    }

    public function testClientSecretGetterSetter(): void
    {
        $clientSecret = 'test_client_secret_456';
        $this->request->setClientSecret($clientSecret);

        $this->assertEquals($clientSecret, $this->request->getClientSecret());
    }

    public function testRefreshTokenGetterSetter(): void
    {
        $refreshToken = 'test_refresh_token_789';
        $this->request->setRefreshToken($refreshToken);

        $this->assertEquals($refreshToken, $this->request->getRefreshToken());
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/api/router', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $this->request->setClientId('test_client');
        $this->request->setClientSecret('test_secret');
        $this->request->setRefreshToken('test_refresh');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('headers', $options);

        // 检查 headers
        $this->assertEquals([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], $options['headers']);

        // 解析 body 参数
        $this->assertIsString($options['body']);
        parse_str($options['body'], $params);

        $this->assertEquals('test_client', $params['client_id']);
        $this->assertEquals('test_refresh', $params['refresh_token']);
        $this->assertEquals('pdd.pop.auth.token.refresh', $params['type']);
        $this->assertEquals('JSON', $params['data_type']);
        $this->assertEquals('V1', $params['version']);
        $this->assertArrayHasKey('timestamp', $params);
        $this->assertArrayHasKey('sign', $params);

        // 验证时间戳是数字字符串
        $this->assertIsNumeric($params['timestamp']);

        // 验证签名格式（32位MD5大写）
        $this->assertIsString($params['sign']);
        $this->assertMatchesRegularExpression('/^[A-F0-9]{32}$/', $params['sign']);
    }

    public function testSignGenerationConsistency(): void
    {
        $this->request->setClientId('test_client');
        $this->request->setClientSecret('test_secret');
        $this->request->setRefreshToken('test_refresh');

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertIsArray($options1);
        $this->assertArrayHasKey('body', $options1);
        $this->assertIsString($options1['body']);
        $this->assertIsArray($options2);
        $this->assertArrayHasKey('body', $options2);
        $this->assertIsString($options2['body']);

        parse_str($options1['body'], $params1);
        parse_str($options2['body'], $params2);

        // 相同时间戳应该生成相同签名
        if ($params1['timestamp'] === $params2['timestamp']) {
            $this->assertEquals($params1['sign'], $params2['sign']);
        }
    }

    public function testSignIncludesAllParameters(): void
    {
        $this->request->setClientId('test');
        $this->request->setClientSecret('secret');
        $this->request->setRefreshToken('refresh');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);
        $this->assertIsString($options['body']);

        parse_str($options['body'], $params);

        // 验证签名不为空且长度正确
        $this->assertIsString($params['sign']);
        $this->assertNotEmpty($params['sign']);
        $this->assertEquals(32, strlen($params['sign']));
    }
}
