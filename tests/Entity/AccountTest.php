<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Enum\ApplicationType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $account = new Account();
        $this->assertInstanceOf(Account::class, $account);
    }

    public function testGetAndSetTitle(): void
    {
        $account = new Account();
        $title = 'Test Application';
        $account->setTitle($title);
        $this->assertSame($title, $account->getTitle());
    }

    public function testGetAndSetClientId(): void
    {
        $account = new Account();
        $clientId = 'test_client_id_123';
        $account->setClientId($clientId);
        $this->assertSame($clientId, $account->getClientId());
    }

    public function testGetAndSetClientSecret(): void
    {
        $account = new Account();
        $clientSecret = 'test_client_secret_456';
        $account->setClientSecret($clientSecret);
        $this->assertSame($clientSecret, $account->getClientSecret());
    }

    public function testGetAndSetApplicationType(): void
    {
        $account = new Account();
        $this->assertNull($account->getApplicationType());
        $account->setApplicationType(ApplicationType::进销存);
        $this->assertSame(ApplicationType::进销存, $account->getApplicationType());
    }

    public function testAuthLogsManagement(): void
    {
        $account = new Account();
        $this->assertCount(0, $account->getAuthLogs());
        $authLog = new AuthLog();
        $account->addAuthLog($authLog);
        $this->assertCount(1, $account->getAuthLogs());
        $this->assertTrue($account->getAuthLogs()->contains($authLog));
        $this->assertSame($account, $authLog->getAccount());
        $account->removeAuthLog($authLog);
        $this->assertCount(0, $account->getAuthLogs());
        $this->assertFalse($account->getAuthLogs()->contains($authLog));
    }

    public function testToString(): void
    {
        $account = new Account();
        $this->assertSame('', $account->__toString());
    }

    protected function createEntity(): object
    {
        return new Account();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'title' => ['title', 'Test Application'],
            'clientId' => ['clientId', 'test_client_id_123'],
            'clientSecret' => ['clientSecret', 'test_client_secret_456'],
        ];
    }
}
