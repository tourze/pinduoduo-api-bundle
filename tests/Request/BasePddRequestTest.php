<?php

namespace PinduoduoApiBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Request\BasePddRequest;

/**
 * @internal
 */
#[CoversClass(BasePddRequest::class)]
final class BasePddRequestTest extends RequestTestCase
{
    private BasePddRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        $this->request = new BasePddRequest();
        $this->account = new Account();
    }

    public function testImplementsRequestInterface(): void
    {
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    public function testAccountGetterSetter(): void
    {
        $this->request->setAccount($this->account);

        $this->assertSame($this->account, $this->request->getAccount());
    }

    public function testTypeGetterSetter(): void
    {
        $type = 'pdd.api.test';
        $this->request->setType($type);

        $this->assertEquals($type, $this->request->getType());
    }

    public function testParamsGetterSetter(): void
    {
        $params = ['key' => 'value', 'number' => 123];
        $this->request->setParams($params);

        $this->assertEquals($params, $this->request->getParams());
    }

    public function testParamsDefaultEmptyArray(): void
    {
        $this->assertEquals([], $this->request->getParams());
    }

    public function testAccessTokenGetterSetter(): void
    {
        $token = 'test_access_token_123';
        $this->request->setAccessToken($token);

        $this->assertEquals($token, $this->request->getAccessToken());
    }

    public function testAccessTokenDefaultNull(): void
    {
        $this->assertNull($this->request->getAccessToken());
    }

    public function testAccessTokenCanBeSetToNull(): void
    {
        $this->request->setAccessToken('some_token');
        $this->request->setAccessToken(null);

        $this->assertNull($this->request->getAccessToken());
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
        $params = ['test' => 'data'];
        $this->request->setParams($params);

        $expected = ['json' => $params];
        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function testGetRequestOptionsWithEmptyParams(): void
    {
        $expected = ['json' => []];
        $this->assertEquals($expected, $this->request->getRequestOptions());
    }
}
