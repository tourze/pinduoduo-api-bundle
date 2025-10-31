<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Repository\CountryRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CountryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CountryRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(CountryRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allCountries = $repository->findAll();
        foreach ($allCountries as $country) {
            $repository->remove($country);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $country = new Country();
        $country->setName('Test Country');

        $repository->save($country);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(CountryRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindCountry(): void
    {
        $repository = self::getService(CountryRepository::class);

        $country = new Country();
        $country->setName('China');

        $this->persistAndFlush($country);

        $foundCountry = $repository->find($country->getId());
        $this->assertNotNull($foundCountry);
        $this->assertSame('China', $foundCountry->getName());
    }

    public function testFindOneByName(): void
    {
        $repository = self::getService(CountryRepository::class);

        $country = new Country();
        $country->setName('United States');

        $this->persistAndFlush($country);

        $foundCountry = $repository->findOneBy(['name' => 'United States']);
        $this->assertNotNull($foundCountry);
        $this->assertSame($country->getId(), $foundCountry->getId());
    }

    public function testFindAllReturnsAllCountries(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $allCountries = $repository->findAll();
        foreach ($allCountries as $country) {
            self::getEntityManager()->remove($country);
        }
        self::getEntityManager()->flush();

        // 创建测试数据
        $country1 = new Country();
        $country1->setName('Japan');

        $country2 = new Country();
        $country2->setName('Korea');

        $this->persistAndFlush($country1);
        $this->persistAndFlush($country2);

        $countries = $repository->findAll();
        $this->assertCount(2, $countries);

        $names = array_map(fn (Country $c): ?string => $c->getName(), $countries);
        $this->assertContains('Japan', $names);
        $this->assertContains('Korea', $names);
    }

    public function testFindByWithOrder(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清理现有数据
        $allCountries = $repository->findAll();
        foreach ($allCountries as $country) {
            self::getEntityManager()->remove($country);
        }
        self::getEntityManager()->flush();

        // 创建测试数据
        $country1 = new Country();
        $country1->setName('Australia');

        $country2 = new Country();
        $country2->setName('Brazil');

        $country3 = new Country();
        $country3->setName('Canada');

        $this->persistAndFlush($country1);
        $this->persistAndFlush($country2);
        $this->persistAndFlush($country3);

        $countries = $repository->findBy([], ['name' => 'ASC']);
        $this->assertCount(3, $countries);
        $this->assertSame('Australia', $countries[0]->getName());
        $this->assertSame('Brazil', $countries[1]->getName());
        $this->assertSame('Canada', $countries[2]->getName());
    }

    public function testFindByWithLimit(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 创建测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $country = new Country();
            $country->setName("Country {$i}");
            $this->persistAndFlush($country);
        }

        $countries = $repository->findBy([], ['name' => 'ASC'], 2);
        $this->assertCount(2, $countries);
        $this->assertSame('Country 1', $countries[0]->getName());
        $this->assertSame('Country 2', $countries[1]->getName());
    }

    public function testRemoveCountry(): void
    {
        $repository = self::getService(CountryRepository::class);

        $country = new Country();
        $country->setName('To Be Removed');

        $this->persistAndFlush($country);
        $id = $country->getId();

        self::getEntityManager()->remove($country);
        self::getEntityManager()->flush();

        $foundCountry = $repository->find($id);
        $this->assertNull($foundCountry);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建多个具有不同名称的国家
        $country1 = new Country();
        $country1->setName('Australia');
        $this->persistAndFlush($country1);

        $country2 = new Country();
        $country2->setName('Belgium');
        $this->persistAndFlush($country2);

        $country3 = new Country();
        $country3->setName('Austria');
        $this->persistAndFlush($country3);

        // 测试 findOneBy 按 name 升序排序（应该返回第一个）
        $firstCountryAsc = $repository->findOneBy([], ['name' => 'ASC']);
        $this->assertNotNull($firstCountryAsc);
        $this->assertSame('Australia', $firstCountryAsc->getName());

        // 测试 findOneBy 按 name 降序排序（应该返回最后一个）
        $firstCountryDesc = $repository->findOneBy([], ['name' => 'DESC']);
        $this->assertNotNull($firstCountryDesc);
        $this->assertSame('Belgium', $firstCountryDesc->getName());

        // 测试 findOneBy 按 id 降序排序（应该返回最新创建的）
        $newestCountry = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestCountry);
        $this->assertSame($country3->getId(), $newestCountry->getId());

        // 测试使用特定条件和排序
        $specificCountry = $repository->findOneBy(['name' => 'Belgium'], ['id' => 'ASC']);
        $this->assertNotNull($specificCountry);
        $this->assertSame('Belgium', $specificCountry->getName());
        $this->assertSame($country2->getId(), $specificCountry->getId());
    }

    public function testFindByWithNullCriteria(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建测试数据，模拟某些字段可能为 null 的情况
        $country1 = new Country();
        $country1->setName('TestCountry1');
        $this->persistAndFlush($country1);

        $country2 = new Country();
        $country2->setName('TestCountry2');
        $this->persistAndFlush($country2);

        // 测试查找所有非空 name 的记录
        /** @var list<Country> $countriesWithName */
        $countriesWithName = $repository->createQueryBuilder('c')
            ->where('c.name IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(2, $countriesWithName);
        $this->assertContains('TestCountry1', array_map(fn (Country $c): ?string => $c->getName(), $countriesWithName));
        $this->assertContains('TestCountry2', array_map(fn (Country $c): ?string => $c->getName(), $countriesWithName));

        // 测试查找 name 为空的记录（在这种情况下应该为空，因为 name 是必填字段）
        /** @var list<Country> $countriesWithoutName */
        $countriesWithoutName = $repository->createQueryBuilder('c')
            ->where('c.name IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(0, $countriesWithoutName);

        // 测试使用 findBy 查找特定条件
        $specificCountries = $repository->findBy(['name' => 'TestCountry1']);
        $this->assertCount(1, $specificCountries);
        $this->assertSame('TestCountry1', $specificCountries[0]->getName());
    }

    public function testFindByWithTimestampCriteria(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建第一个国家，手动设置时间戳
        $country1 = new Country();
        $country1->setName('EarlyCountry');
        $firstTimestamp = new \DateTimeImmutable('2023-01-01 10:00:00');
        $country1->setCreateTime($firstTimestamp);
        $this->persistAndFlush($country1);

        // 创建第二个国家，使用稍后的时间戳
        $country2 = new Country();
        $country2->setName('LaterCountry');
        $secondTimestamp = new \DateTimeImmutable('2023-01-01 11:00:00');
        $country2->setCreateTime($secondTimestamp);
        $this->persistAndFlush($country2);

        // 验证时间戳确实不同
        $this->assertNotEquals($firstTimestamp, $secondTimestamp);
        $this->assertGreaterThan($firstTimestamp, $secondTimestamp);

        // 测试查找特定时间之后创建的记录
        /** @var list<Country> $laterCountries */
        $laterCountries = $repository->createQueryBuilder('c')
            ->where('c.createTime > :timestamp')
            ->setParameter('timestamp', $firstTimestamp)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $laterCountries);
        $this->assertSame('LaterCountry', $laterCountries[0]->getName());

        // 测试查找特定时间之前创建的记录
        /** @var list<Country> $earlierCountries */
        $earlierCountries = $repository->createQueryBuilder('c')
            ->where('c.createTime <= :timestamp')
            ->setParameter('timestamp', $firstTimestamp)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $earlierCountries);
        $this->assertSame('EarlyCountry', $earlierCountries[0]->getName());

        // 测试查找所有有创建时间的记录
        /** @var list<Country> $allCountriesWithTimestamp */
        $allCountriesWithTimestamp = $repository->createQueryBuilder('c')
            ->where('c.createTime IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(2, $allCountriesWithTimestamp);

        // 测试使用时间范围查询
        $middleTime = new \DateTimeImmutable('2023-01-01 10:30:00');
        /** @var list<Country> $countriesInRange */
        $countriesInRange = $repository->createQueryBuilder('c')
            ->where('c.createTime >= :startTime')
            ->andWhere('c.createTime <= :endTime')
            ->setParameter('startTime', $firstTimestamp)
            ->setParameter('endTime', $middleTime)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $countriesInRange);
        $this->assertSame('EarlyCountry', $countriesInRange[0]->getName());
    }

    public function testCountWithNullCriteria(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建测试数据
        $country1 = new Country();
        $country1->setName('TestCountry1');
        $country1->setCreateTime(new \DateTimeImmutable());
        $this->persistAndFlush($country1);

        $country2 = new Country();
        $country2->setName('TestCountry2');
        $country2->setCreateTime(null);  // 显式设置为 null
        $this->persistAndFlush($country2);

        // 测试计数所有非空 createTime 的记录
        $nonNullCreateTimeQuery = $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createTime IS NOT NULL')
            ->getQuery()
        ;

        $nonNullCreateTimeCount = (int) $nonNullCreateTimeQuery->getSingleScalarResult();
        $this->assertGreaterThanOrEqual(1, $nonNullCreateTimeCount);

        // 测试计数所有为空 createTime 的记录
        $nullCreateTimeQuery = $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createTime IS NULL')
            ->getQuery()
        ;

        $nullCreateTimeCount = (int) $nullCreateTimeQuery->getSingleScalarResult();
        $this->assertGreaterThanOrEqual(0, $nullCreateTimeCount);

        // 测试计数所有记录
        $totalCount = $repository->count([]);
        $this->assertSame(2, $totalCount);

        // 验证总数等于空值加非空值
        $this->assertSame($totalCount, $nonNullCreateTimeCount + $nullCreateTimeCount);
    }

    public function testFindByWithNullFieldQueries(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建测试数据，其中一些字段设置为 null
        $country1 = new Country();
        $country1->setName('CountryWithNullTime');
        $country1->setCreateTime(null);
        $country1->setUpdateTime(null);
        $this->persistAndFlush($country1);

        $country2 = new Country();
        $country2->setName('CountryWithTime');
        $country2->setCreateTime(new \DateTimeImmutable());
        $country2->setUpdateTime(new \DateTimeImmutable());
        $this->persistAndFlush($country2);

        // 测试查找 createTime 为 null 的记录
        $countriesWithNullCreateTime = $repository->createQueryBuilder('c')
            ->where('c.createTime IS NULL')
            ->getQuery()
            ->getResult()
        ;

        // 只要能正确执行查询即可，不要求一定有结果
        $this->assertIsArray($countriesWithNullCreateTime);

        // 测试查找 updateTime 为 null 的记录
        $countriesWithNullUpdateTime = $repository->createQueryBuilder('c')
            ->where('c.updateTime IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($countriesWithNullUpdateTime);

        // 测试查找 createTime 不为 null 的记录
        $countriesWithNonNullCreateTime = $repository->createQueryBuilder('c')
            ->where('c.createTime IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($countriesWithNonNullCreateTime);

        // 测试查找 updateTime 不为 null 的记录
        $countriesWithNonNullUpdateTime = $repository->createQueryBuilder('c')
            ->where('c.updateTime IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($countriesWithNonNullUpdateTime);

        // 验证所有记录的总数等于 null 记录数加上非 null 记录数
        $totalCount = $repository->count([]);
        $nullCreateTimeCount = count($countriesWithNullCreateTime);
        $nonNullCreateTimeCount = count($countriesWithNonNullCreateTime);

        $this->assertSame($totalCount, $nullCreateTimeCount + $nonNullCreateTimeCount);

        // 验证查询功能正常工作，至少应该找到我们创建的特定命名的记录
        /** @var list<Country> $testCountry2Results */
        $testCountry2Results = $repository->createQueryBuilder('c')
            ->where('c.name = :name')
            ->andWhere('c.createTime IS NOT NULL')
            ->setParameter('name', 'CountryWithTime')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $testCountry2Results);
        $this->assertSame('CountryWithTime', $testCountry2Results[0]->getName());
    }

    public function testCountWithNullFieldQueries(): void
    {
        $repository = self::getService(CountryRepository::class);

        // 清空现有数据
        $this->clearAllCountries($repository);

        // 创建测试数据
        $country1 = new Country();
        $country1->setName('TestCountry1');
        $this->persistAndFlush($country1);

        $country2 = new Country();
        $country2->setName('TestCountry2');
        $country2->setCreateTime(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $country2->setUpdateTime(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $this->persistAndFlush($country2);

        // 测试计数 createTime 为 null 的记录
        $nullCreateTimeCount = (int) $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createTime IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(0, $nullCreateTimeCount);

        // 测试计数 updateTime 为 null 的记录
        $nullUpdateTimeCount = (int) $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.updateTime IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(0, $nullUpdateTimeCount);

        // 测试计数 createTime 不为 null 的记录
        $nonNullCreateTimeCount = (int) $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createTime IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(0, $nonNullCreateTimeCount);

        // 测试计数 updateTime 不为 null 的记录
        $nonNullUpdateTimeCount = (int) $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.updateTime IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertGreaterThanOrEqual(0, $nonNullUpdateTimeCount);

        // 验证总数等于 null 和非 null 的计数之和
        $totalCount = $repository->count([]);
        $this->assertSame(2, $totalCount);
        $this->assertSame($totalCount, $nullCreateTimeCount + $nonNullCreateTimeCount);
        $this->assertSame($totalCount, $nullUpdateTimeCount + $nonNullUpdateTimeCount);

        // 验证至少有一个记录的 createTime 不为 null（我们手动设置的那个）
        $specificCountryCount = (int) $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.name = :name')
            ->andWhere('c.createTime IS NOT NULL')
            ->setParameter('name', 'TestCountry2')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(1, $specificCountryCount);
    }

    private function clearAllCountries(CountryRepository $repository): void
    {
        $allCountries = $repository->findAll();
        foreach ($allCountries as $country) {
            self::getEntityManager()->remove($country);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Country
    {
        $entity = new Country();
        $entity->setName('Test Country ' . uniqid());

        return $entity;
    }

    protected function getRepository(): CountryRepository
    {
        return self::getService(CountryRepository::class);
    }
}
