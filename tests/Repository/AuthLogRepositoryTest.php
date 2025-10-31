<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Entity\AuthLog;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AuthLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class AuthLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(AuthLogRepository::class);
        $entityManager = self::getEntityManager();

        // 清理现有数据，避免 DataFixtures 检查失败
        $allAuthLogs = $repository->findAll();
        foreach ($allAuthLogs as $authLog) {
            $entityManager->remove($authLog);
        }
        $entityManager->flush();

        // 创建关联的 Account 和 Mall
        $account = new Account();
        $account->setTitle('DataFixture Test Account for AuthLog');
        $account->setClientId('authlog_test_client');
        $account->setClientSecret('authlog_test_secret');
        $entityManager->persist($account);

        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for AuthLog');
        $mall->setDescription('Test mall for authlog data fixtures');
        $entityManager->persist($mall);

        // 添加一个测试数据以满足 DataFixtures 检查
        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        $authLog->setAccessToken('test_access_token');
        $authLog->setRefreshToken('test_refresh_token');
        $authLog->setScope(['read', 'write']);
        $authLog->setContext(['test' => true]);

        $entityManager->persist($authLog);
        $entityManager->flush();

        // 清理实体管理器状态以确保连接状态测试正常
        $entityManager->clear();
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindAuthLog(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account = new Account();
        $account->setTitle('Test Account');
        $account->setClientId('test_client');
        $account->setClientSecret('test_secret');
        $this->persistAndFlush($account);

        $mall = new Mall();
        $mall->setName('Test Mall');
        $this->persistAndFlush($mall);

        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        $authLog->setAccessToken('test_access_token');
        $authLog->setRefreshToken('test_refresh_token');
        $authLog->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));
        $authLog->setScope(['read', 'write']);
        $authLog->setContext(['user_id' => 123]);

        $this->persistAndFlush($authLog);

        $foundAuthLog = $repository->find($authLog->getId());
        $this->assertNotNull($foundAuthLog);
        $this->assertSame('test_access_token', $foundAuthLog->getAccessToken());
        $this->assertSame('test_refresh_token', $foundAuthLog->getRefreshToken());
        $this->assertSame(['read', 'write'], $foundAuthLog->getScope());
        $this->assertSame(['user_id' => 123], $foundAuthLog->getContext());
    }

    public function testFindByAccount(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account = new Account();
        $account->setTitle('Account For Logs');
        $account->setClientId('account_client');
        $account->setClientSecret('account_secret');
        $this->persistAndFlush($account);

        $mall1 = new Mall();
        $mall1->setName('Mall 1');
        $this->persistAndFlush($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall 2');
        $this->persistAndFlush($mall2);

        // 创建多个 AuthLog 实体
        $authLog1 = new AuthLog();
        $authLog1->setAccount($account);
        $authLog1->setMall($mall1);
        $authLog1->setAccessToken('token1');
        $this->persistAndFlush($authLog1);

        $authLog2 = new AuthLog();
        $authLog2->setAccount($account);
        $authLog2->setMall($mall2);
        $authLog2->setAccessToken('token2');
        $this->persistAndFlush($authLog2);

        $authLogs = $repository->findBy(['account' => $account]);
        $this->assertCount(2, $authLogs);
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account1 = new Account();
        $account1->setTitle('Account 1');
        $account1->setClientId('client1');
        $account1->setClientSecret('secret1');
        $this->persistAndFlush($account1);

        $account2 = new Account();
        $account2->setTitle('Account 2');
        $account2->setClientId('client2');
        $account2->setClientSecret('secret2');
        $this->persistAndFlush($account2);

        $mall = new Mall();
        $mall->setName('Shared Mall');
        $this->persistAndFlush($mall);

        // 创建多个 AuthLog 实体
        $authLog1 = new AuthLog();
        $authLog1->setAccount($account1);
        $authLog1->setMall($mall);
        $authLog1->setAccessToken('token1');
        $this->persistAndFlush($authLog1);

        $authLog2 = new AuthLog();
        $authLog2->setAccount($account2);
        $authLog2->setMall($mall);
        $authLog2->setAccessToken('token2');
        $this->persistAndFlush($authLog2);

        $authLogs = $repository->findBy(['mall' => $mall]);
        $this->assertCount(2, $authLogs);
    }

    public function testFindByAccessToken(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account = new Account();
        $account->setTitle('Account For Token');
        $account->setClientId('token_account');
        $account->setClientSecret('token_secret');
        $this->persistAndFlush($account);

        $mall = new Mall();
        $mall->setName('Mall For Token');
        $this->persistAndFlush($mall);

        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        $authLog->setAccessToken('unique_access_token');
        $this->persistAndFlush($authLog);

        $foundAuthLog = $repository->findOneBy(['accessToken' => 'unique_access_token']);
        $this->assertNotNull($foundAuthLog);
        $accountFromLog = $foundAuthLog->getAccount();
        $this->assertNotNull($accountFromLog);
        $this->assertSame($account->getId(), $accountFromLog->getId());
    }

    public function testFindWithExpiredToken(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account = new Account();
        $account->setTitle('Account For Expired');
        $account->setClientId('expired_account');
        $account->setClientSecret('expired_secret');
        $this->persistAndFlush($account);

        $mall = new Mall();
        $mall->setName('Mall For Expired');
        $this->persistAndFlush($mall);

        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        $authLog->setAccessToken('expired_token');
        $authLog->setTokenExpireTime(new \DateTimeImmutable('-1 hour'));
        $this->persistAndFlush($authLog);

        $foundAuthLog = $repository->findOneBy(['accessToken' => 'expired_token']);
        $this->assertNotNull($foundAuthLog);
        $this->assertLessThan(new \DateTimeImmutable(), $foundAuthLog->getTokenExpireTime());
    }

    public function testFindWithNullAccessToken(): void
    {
        $repository = self::getService(AuthLogRepository::class);

        // 创建 Account 和 Mall 实体
        $account = new Account();
        $account->setTitle('Account For Null Token');
        $account->setClientId('null_token_account');
        $account->setClientSecret('null_token_secret');
        $this->persistAndFlush($account);

        $mall = new Mall();
        $mall->setName('Mall For Null Token');
        $this->persistAndFlush($mall);

        $authLog = new AuthLog();
        $authLog->setAccount($account);
        $authLog->setMall($mall);
        // 不设置 accessToken，保持为 null
        $this->persistAndFlush($authLog);

        $nullTokenLogs = $repository->findBy(['accessToken' => null]);
        $this->assertGreaterThan(0, count($nullTokenLogs));
    }

    protected function createNewEntity(): AuthLog
    {
        $entity = new AuthLog();
        $entity->setAccessToken('test_token_' . uniqid());
        $entity->setRefreshToken('test_refresh_' . uniqid());
        $entity->setScope(['read', 'write']);
        $entity->setContext(['test' => true]);

        return $entity;
    }

    protected function getRepository(): AuthLogRepository
    {
        return self::getService(AuthLogRepository::class);
    }
}
