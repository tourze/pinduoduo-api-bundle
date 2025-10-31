<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Account;
use PinduoduoApiBundle\Repository\AccountRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(AccountRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $this->assertInstanceOf(Account::class, $account);
            $repository->remove($account);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $account = new Account();
        $account->setTitle('DataFixture Test Account');
        $account->setClientId('datafixture_test_client');
        $account->setClientSecret('datafixture_test_secret');
        $repository->save($account);

        // 清理实体管理器状态以确保连接状态测试正常
        self::getEntityManager()->clear();
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(AccountRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindAccount(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setTitle('Test Account');
        $account->setClientId('test_client_id');
        $account->setClientSecret('test_client_secret');

        $repository->save($account);

        $foundAccount = $repository->find($account->getId());
        $this->assertNotNull($foundAccount);
        $this->assertSame('Test Account', $foundAccount->getTitle());
        $this->assertSame('test_client_id', $foundAccount->getClientId());
        $this->assertSame('test_client_secret', $foundAccount->getClientSecret());
    }

    public function testFindOneByClientId(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setTitle('Another Account');
        $account->setClientId('unique_client_id');
        $account->setClientSecret('secret_key');

        $repository->save($account);

        $foundAccount = $repository->findOneBy(['clientId' => 'unique_client_id']);
        $this->assertNotNull($foundAccount);
        $this->assertSame('Another Account', $foundAccount->getTitle());
    }

    public function testFindAllReturnsAllAccounts(): void
    {
        $repository = self::getService(AccountRepository::class);

        // 清空现有数据
        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $repository->remove($account);
        }

        // 创建测试数据
        $account1 = new Account();
        $account1->setTitle('Account 1');
        $account1->setClientId('client1');
        $account1->setClientSecret('secret1');

        $account2 = new Account();
        $account2->setTitle('Account 2');
        $account2->setClientId('client2');
        $account2->setClientSecret('secret2');

        $repository->save($account1);
        $repository->save($account2);

        $accounts = $repository->findAll();
        $this->assertCount(2, $accounts);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(AccountRepository::class);

        // 清理现有数据
        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $repository->remove($account);
        }

        // 创建测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $account = new Account();
            $account->setTitle("Account {$i}");
            $account->setClientId("client{$i}");
            $account->setClientSecret("secret{$i}");
            $repository->save($account);
        }

        $accounts = $repository->findBy([], ['title' => 'ASC'], 2, 1);
        $this->assertCount(2, $accounts);
        $this->assertSame('Account 2', $accounts[0]->getTitle());
        $this->assertSame('Account 3', $accounts[1]->getTitle());
    }

    public function testRemoveAccount(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setTitle('To Be Removed');
        $account->setClientId('remove_client');
        $account->setClientSecret('remove_secret');

        $repository->save($account);
        $id = $account->getId();

        $repository->remove($account);

        $foundAccount = $repository->find($id);
        $this->assertNull($foundAccount);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(AccountRepository::class);

        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $repository->remove($account);
        }

        $account1 = new Account();
        $account1->setTitle('Alpha Account');
        $account1->setClientId('alpha_client');
        $account1->setClientSecret('alpha_secret');
        $repository->save($account1);

        $account2 = new Account();
        $account2->setTitle('Beta Account');
        $account2->setClientId('beta_client');
        $account2->setClientSecret('beta_secret');
        $repository->save($account2);

        $account3 = new Account();
        $account3->setTitle('Gamma Account');
        $account3->setClientId('gamma_client');
        $account3->setClientSecret('gamma_secret');
        $repository->save($account3);

        $firstAccountAsc = $repository->findOneBy([], ['title' => 'ASC']);
        $this->assertNotNull($firstAccountAsc);
        $this->assertSame('Alpha Account', $firstAccountAsc->getTitle());

        $firstAccountDesc = $repository->findOneBy([], ['title' => 'DESC']);
        $this->assertNotNull($firstAccountDesc);
        $this->assertSame('Gamma Account', $firstAccountDesc->getTitle());

        $newestAccount = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestAccount);
        $this->assertSame($account3->getId(), $newestAccount->getId());

        $specificAccount = $repository->findOneBy(['title' => 'Beta Account'], ['id' => 'ASC']);
        $this->assertNotNull($specificAccount);
        $this->assertSame('Beta Account', $specificAccount->getTitle());
        $this->assertSame($account2->getId(), $specificAccount->getId());
    }

    public function testFindByWithNullCriteria(): void
    {
        $repository = self::getService(AccountRepository::class);

        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $repository->remove($account);
        }

        $account1 = new Account();
        $account1->setTitle('Null Test Account');
        $account1->setClientId('null_test_client');
        $account1->setClientSecret('null_test_secret');
        $repository->save($account1);

        $accountsWithData = $repository->createQueryBuilder('a')
            ->where('a.title IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($accountsWithData);
        $this->assertGreaterThanOrEqual(1, count($accountsWithData));

        $found = false;
        foreach ($accountsWithData as $account) {
            if ($account instanceof Account && 'Null Test Account' === $account->getTitle()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCountWithNullCriteria(): void
    {
        $repository = self::getService(AccountRepository::class);

        $allAccounts = $repository->findAll();
        foreach ($allAccounts as $account) {
            $repository->remove($account);
        }

        $account1 = new Account();
        $account1->setTitle('Null Count Account');
        $account1->setClientId('null_count_client');
        $account1->setClientSecret('null_count_secret');
        $repository->save($account1);

        $nonNullTitleCount = (int) $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.title IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(1, $nonNullTitleCount);

        $totalCount = $repository->count([]);
        $this->assertSame(1, $totalCount);
        $this->assertSame($totalCount, $nonNullTitleCount);
    }

    protected function createNewEntity(): Account
    {
        $entity = new Account();
        $entity->setTitle('Test Account ' . uniqid());
        $entity->setClientId('test_client_' . uniqid());
        $entity->setClientSecret('test_secret_' . uniqid());

        return $entity;
    }

    protected function getRepository(): AccountRepository
    {
        return self::getService(AccountRepository::class);
    }
}
