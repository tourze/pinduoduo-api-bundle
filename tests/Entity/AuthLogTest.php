<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AuthLog::class)]
final class AuthLogTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $authLog = new AuthLog();
        $this->assertInstanceOf(AuthLog::class, $authLog);
    }

    public function testGetAndSetContext(): void
    {
        $authLog = new AuthLog();
        $context = ['key' => 'value', 'another' => 'data'];
        $this->assertSame([], $authLog->getContext());
        $authLog->setContext($context);
        $this->assertSame($context, $authLog->getContext());
    }

    public function testGetAndSetAccount(): void
    {
        $authLog = new AuthLog();
        $account = new Account();
        $this->assertNull($authLog->getAccount());
        $authLog->setAccount($account);
        $this->assertSame($account, $authLog->getAccount());
    }

    public function testGetAndSetMall(): void
    {
        $authLog = new AuthLog();
        $mall = new Mall();
        $this->assertNull($authLog->getMall());
        $authLog->setMall($mall);
        $this->assertSame($mall, $authLog->getMall());
    }

    public function testGetAndSetAccessToken(): void
    {
        $authLog = new AuthLog();
        $accessToken = 'test_access_token_123';
        $this->assertNull($authLog->getAccessToken());
        $authLog->setAccessToken($accessToken);
        $this->assertSame($accessToken, $authLog->getAccessToken());
    }

    public function testGetAndSetRefreshToken(): void
    {
        $authLog = new AuthLog();
        $refreshToken = 'test_refresh_token_456';
        $this->assertNull($authLog->getRefreshToken());
        $authLog->setRefreshToken($refreshToken);
        $this->assertSame($refreshToken, $authLog->getRefreshToken());
    }

    public function testGetAndSetTokenExpireTime(): void
    {
        $authLog = new AuthLog();
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $this->assertNull($authLog->getTokenExpireTime());
        $authLog->setTokenExpireTime($expireTime);
        $this->assertSame($expireTime, $authLog->getTokenExpireTime());
    }

    public function testGetAndSetScope(): void
    {
        $authLog = new AuthLog();
        $scope = ['read', 'write', 'admin'];
        $this->assertNull($authLog->getScope());
        $authLog->setScope($scope);
        $this->assertSame($scope, $authLog->getScope());
    }

    public function testToString(): void
    {
        $authLog = new AuthLog();
        $this->assertSame('', $authLog->__toString());
    }

    protected function createEntity(): object
    {
        return new AuthLog();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'context' => ['context', ['key' => 'value']],
            'accessToken' => ['accessToken', 'test_token_123'],
            'refreshToken' => ['refreshToken', 'refresh_token_456'],
            'tokenExpireTime' => ['tokenExpireTime', new \DateTimeImmutable('2024-12-31 23:59:59')],
            'scope' => ['scope', ['read', 'write']],
        ];
    }
}
