<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Entity\Stock\DepotPriority;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotPriorityTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use PinduoduoApiBundle\Repository\Stock\DepotPriorityRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DepotPriorityRepository::class)]
#[RunTestsInSeparateProcesses]
final class DepotPriorityRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(DepotPriorityRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allDepotPriorities = $repository->findAll();
        foreach ($allDepotPriorities as $depotPriority) {
            $repository->remove($depotPriority);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $depotPriority = new DepotPriority();
        $depotPriority->setDepotCode('TEST_DEPOT');
        $depotPriority->setDepotName('Test Depot');
        $depotPriority->setPriority(1);
        $depotPriority->setProvinceId(1);
        $depotPriority->setCityId(1);
        $depotPriority->setDistrictId(1);

        $repository->save($depotPriority);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindDepotPriority(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        // 创建依赖的Depot实体
        $depot = new Depot();
        $depot->setDepotCode('DEPOT001');
        $depot->setDepotName('测试仓库');
        $depot->setDepotAlias('Test Depot');
        $depot->setContact('测试联系人');
        $depot->setPhone('13800138000');
        $depot->setAddress('测试地址');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $depotPriority = new DepotPriority();
        $depotPriority->setDepot($depot);
        $depotPriority->setDepotCode('DEPOT001');
        $depotPriority->setDepotName('测试仓库');
        $depotPriority->setProvinceId(110000);
        $depotPriority->setCityId(110100);
        $depotPriority->setDistrictId(110101);
        $depotPriority->setPriority(1);
        $depotPriority->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $depotPriority->setStatus(DepotStatusEnum::ACTIVE);

        $repository->save($depotPriority);

        $foundDepotPriority = $repository->find($depotPriority->getId());
        $this->assertNotNull($foundDepotPriority);
        $this->assertSame('DEPOT001', $foundDepotPriority->getDepotCode());
        $this->assertSame('测试仓库', $foundDepotPriority->getDepotName());
        $this->assertSame(110000, $foundDepotPriority->getProvinceId());
        $this->assertSame(110100, $foundDepotPriority->getCityId());
        $this->assertSame(110101, $foundDepotPriority->getDistrictId());
        $this->assertSame(1, $foundDepotPriority->getPriority());
        $this->assertSame(DepotPriorityTypeEnum::NORMAL, $foundDepotPriority->getPriorityType());
        $this->assertSame(DepotStatusEnum::ACTIVE, $foundDepotPriority->getStatus());
    }

    public function testFindOneByDepotCode(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('UNIQUE_DEPOT');
        $depot->setDepotName('唯一仓库');
        $depot->setDepotAlias('Unique Depot');
        $depot->setContact('唯一联系人');
        $depot->setPhone('13900139000');
        $depot->setAddress('唯一地址');
        $depot->setProvince(310000);
        $depot->setCity(310100);
        $depot->setDistrict(310115);
        $depot->setZipCode('200000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $depotPriority = new DepotPriority();
        $depotPriority->setDepot($depot);
        $depotPriority->setDepotCode('UNIQUE_DEPOT');
        $depotPriority->setDepotName('唯一仓库');
        $depotPriority->setProvinceId(310000);
        $depotPriority->setCityId(310100);
        $depotPriority->setDistrictId(310115);
        $depotPriority->setPriority(2);

        $repository->save($depotPriority);

        $foundDepotPriority = $repository->findOneBy(['depotCode' => 'UNIQUE_DEPOT']);
        $this->assertNotNull($foundDepotPriority);
        $this->assertSame('UNIQUE_DEPOT', $foundDepotPriority->getDepotCode());
        $this->assertSame('唯一仓库', $foundDepotPriority->getDepotName());
    }

    public function testFindByPriorityType(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $depot1 = new Depot();
        $depot1->setDepotCode('NORMAL_DEPOT');
        $depot1->setDepotName('普通仓库');
        $depot1->setDepotAlias('Normal Depot');
        $depot1->setContact('普通联系人');
        $depot1->setPhone('13700137000');
        $depot1->setAddress('普通地址');
        $depot1->setProvince(440000);
        $depot1->setCity(440100);
        $depot1->setDistrict(440106);
        $depot1->setZipCode('510000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('HIGH_DEPOT');
        $depot2->setDepotName('高优先级仓库');
        $depot2->setDepotAlias('High Priority Depot');
        $depot2->setContact('高优先级联系人');
        $depot2->setPhone('13600136000');
        $depot2->setAddress('高优先级地址');
        $depot2->setProvince(440000);
        $depot2->setCity(440300);
        $depot2->setDistrict(440305);
        $depot2->setZipCode('518000');
        $depot2->setType(DepotTypeEnum::SELF_BUILT);
        $depot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot2);

        $priority1 = new DepotPriority();
        $priority1->setDepot($depot1);
        $priority1->setDepotCode('NORMAL_DEPOT');
        $priority1->setDepotName('普通仓库');
        $priority1->setProvinceId(440000);
        $priority1->setCityId(440100);
        $priority1->setDistrictId(440106);
        $priority1->setPriorityType(DepotPriorityTypeEnum::NORMAL);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot2);
        $priority2->setDepotCode('HIGH_DEPOT');
        $priority2->setDepotName('高优先级仓库');
        $priority2->setProvinceId(440000);
        $priority2->setCityId(440300);
        $priority2->setDistrictId(440305);
        $priority2->setPriorityType(DepotPriorityTypeEnum::PREFERRED);

        $repository->save($priority1);
        $repository->save($priority2);

        $normalPriorities = $repository->findBy(['priorityType' => DepotPriorityTypeEnum::NORMAL]);
        $this->assertNotEmpty($normalPriorities);

        $preferredPriorities = $repository->findBy(['priorityType' => DepotPriorityTypeEnum::PREFERRED]);
        $this->assertNotEmpty($preferredPriorities);
    }

    public function testFindByStatus(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('STATUS_DEPOT');
        $depot->setDepotName('状态测试仓库');
        $depot->setDepotAlias('Status Test Depot');
        $depot->setContact('状态测试联系人');
        $depot->setPhone('13500135000');
        $depot->setAddress('状态测试地址');
        $depot->setProvince(510000);
        $depot->setCity(510100);
        $depot->setDistrict(510104);
        $depot->setZipCode('610000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority1 = new DepotPriority();
        $priority1->setDepot($depot);
        $priority1->setDepotCode('STATUS_DEPOT');
        $priority1->setDepotName('状态测试仓库');
        $priority1->setProvinceId(510000);
        $priority1->setCityId(510100);
        $priority1->setDistrictId(510104);
        $priority1->setStatus(DepotStatusEnum::ACTIVE);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot);
        $priority2->setDepotCode('STATUS_DEPOT');
        $priority2->setDepotName('状态测试仓库');
        $priority2->setProvinceId(510000);
        $priority2->setCityId(510100);
        $priority2->setDistrictId(510105);
        $priority2->setStatus(DepotStatusEnum::DISABLED);

        $repository->save($priority1);
        $repository->save($priority2);

        $activePriorities = $repository->findBy(['status' => DepotStatusEnum::ACTIVE]);
        $this->assertNotEmpty($activePriorities);

        $disabledPriorities = $repository->findBy(['status' => DepotStatusEnum::DISABLED]);
        $this->assertNotEmpty($disabledPriorities);
    }

    public function testFindByWithNullDepotId(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $this->clearAllDepotPriorities($repository);

        $depot = new Depot();
        $depot->setDepotCode('NULL_ID_DEPOT');
        $depot->setDepotName('无ID仓库');
        $depot->setDepotAlias('No ID Depot');
        $depot->setContact('无ID联系人');
        $depot->setPhone('13400134000');
        $depot->setAddress('无ID地址');
        $depot->setProvince(610000);
        $depot->setCity(610100);
        $depot->setDistrict(610113);
        $depot->setZipCode('710000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority = new DepotPriority();
        $priority->setDepot($depot);
        $priority->setDepotCode('NULL_ID_DEPOT');
        $priority->setDepotName('无ID仓库');
        $priority->setDepotId(null);
        $priority->setProvinceId(99999);
        $priority->setCityId(99999);
        $priority->setDistrictId(99999);
        $priority->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $priority->setPriority(1);
        $priority->setStatus(DepotStatusEnum::ACTIVE);

        $this->persistAndFlush($priority);

        // 验证记录已被保存
        $allPriorities = $repository->findAll();
        $this->assertNotEmpty($allPriorities, 'No priorities found in database');

        // 直接检查所有记录中是否有我们的测试记录
        $found = false;
        foreach ($allPriorities as $item) {
            if ('NULL_ID_DEPOT' === $item->getDepotCode() && null === $item->getDepotId()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Should find a priority with NULL_ID_DEPOT code and null depotId');
    }

    public function testRemoveDepotPriority(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('REMOVE_DEPOT');
        $depot->setDepotName('待删除仓库');
        $depot->setDepotAlias('To Remove Depot');
        $depot->setContact('待删除联系人');
        $depot->setPhone('13300133000');
        $depot->setAddress('待删除地址');
        $depot->setProvince(420000);
        $depot->setCity(420100);
        $depot->setDistrict(420106);
        $depot->setZipCode('430000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority = new DepotPriority();
        $priority->setDepot($depot);
        $priority->setDepotCode('REMOVE_DEPOT');
        $priority->setDepotName('待删除仓库');
        $priority->setProvinceId(420000);
        $priority->setCityId(420100);
        $priority->setDistrictId(420106);

        $repository->save($priority);
        $id = $priority->getId();

        $repository->remove($priority);

        $foundPriority = $repository->find($id);
        $this->assertNull($foundPriority);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(DepotPriorityRepository::class);

        $this->clearAllDepotPriorities($repository);

        $depot = new Depot();
        $depot->setDepotCode('TEST_DEPOT_' . uniqid());
        $depot->setDepotName('Test Depot');
        $depot->setDepotAlias('Test Depot Alias');
        $depot->setContact('Test Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Test Address');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setStatus(DepotStatusEnum::ACTIVE);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority1 = new DepotPriority();
        $priority1->setDepot($depot);
        $priority1->setDepotCode('FINDONE_DEPOT_1');
        $priority1->setDepotName('Test Depot');
        $priority1->setProvinceId(110000);
        $priority1->setCityId(110100);
        $priority1->setDistrictId(110101);
        $priority1->setPriorityType(DepotPriorityTypeEnum::PREFERRED);
        $priority1->setPriority(30);
        $priority1->setStatus(DepotStatusEnum::ACTIVE);
        $this->persistAndFlush($priority1);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot);
        $priority2->setDepotCode('FINDONE_DEPOT_2');
        $priority2->setDepotName('Test Depot');
        $priority2->setProvinceId(110000);
        $priority2->setCityId(110100);
        $priority2->setDistrictId(110102);
        $priority2->setPriorityType(DepotPriorityTypeEnum::EXCLUSIVE);
        $priority2->setPriority(10);
        $priority2->setStatus(DepotStatusEnum::ACTIVE);
        $this->persistAndFlush($priority2);

        $priority3 = new DepotPriority();
        $priority3->setDepot($depot);
        $priority3->setDepotCode('FINDONE_DEPOT_3');
        $priority3->setDepotName('Test Depot');
        $priority3->setProvinceId(110000);
        $priority3->setCityId(110100);
        $priority3->setDistrictId(110103);
        $priority3->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $priority3->setPriority(20);
        $priority3->setStatus(DepotStatusEnum::ACTIVE);
        $this->persistAndFlush($priority3);

        $firstPriorityAsc = $repository->findOneBy([], ['priority' => 'ASC']);
        $this->assertNotNull($firstPriorityAsc);
        $this->assertSame(10, $firstPriorityAsc->getPriority());

        $firstPriorityDesc = $repository->findOneBy([], ['priority' => 'DESC']);
        $this->assertNotNull($firstPriorityDesc);
        $this->assertSame(30, $firstPriorityDesc->getPriority());

        $newestPriority = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestPriority);
        $this->assertSame($priority3->getId(), $newestPriority->getId());
    }

    private function clearAllDepotPriorities(DepotPriorityRepository $repository): void
    {
        $allPriorities = $repository->findAll();
        foreach ($allPriorities as $priority) {
            self::getEntityManager()->remove($priority);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): DepotPriority
    {
        $depot = new Depot();
        $depot->setDepotCode('TEST_DEPOT_' . uniqid());
        $depot->setDepotName('Test Depot for Priority ' . uniqid());
        $depot->setDepotAlias('Test Depot Alias');
        $depot->setContact('Test Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Test Address');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot->setStatus(DepotStatusEnum::ACTIVE);

        $entity = new DepotPriority();
        $entity->setDepot($depot);
        $entity->setDepotCode($depot->getDepotCode());
        $entity->setDepotName($depot->getDepotName());
        $entity->setProvinceId(110000);
        $entity->setCityId(110100);
        $entity->setDistrictId(110101);
        $entity->setPriorityType(DepotPriorityTypeEnum::NORMAL);
        $entity->setPriority(1);
        $entity->setStatus(DepotStatusEnum::ACTIVE);

        return $entity;
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityShouldPersistedSuccessWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $depot = $entity->getDepot();
        $entityManager->persist($depot);

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    /**
     * 由于基类的测试方法没有处理级联持久化，我们需要提供额外的方法来测试这个场景
     */
    #[Test]
    public function testCreateNewEntityAndDetachShouldNotInIdentityMapWithCascade(): void
    {
        $entity = $this->createNewEntity();
        $this->assertInstanceOf($this->getRepository()->getClassName(), $entity);

        $entityManager = self::getEntityManager();

        // 手动持久化关联的实体
        $depot = $entity->getDepot();
        $entityManager->persist($depot);

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertTrue($entityManager->getUnitOfWork()->isInIdentityMap($entity));

        $entityManager->detach($entity);
        $this->assertFalse($entityManager->getUnitOfWork()->isInIdentityMap($entity));
    }

    protected function getRepository(): DepotPriorityRepository
    {
        return self::getService(DepotPriorityRepository::class);
    }

    public function testFindByDepotCode(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepotPriorities($repository);

        $depot = new Depot();
        $depot->setDepotCode('FIND_BY_CODE_DEPOT');
        $depot->setDepotName('Code Test Depot');
        $depot->setDepotAlias('Code Alias');
        $depot->setContact('Code Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Code Address');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority1 = new DepotPriority();
        $priority1->setDepot($depot);
        $priority1->setDepotCode('FIND_BY_CODE_DEPOT');
        $priority1->setDepotName('Code Test Depot');
        $priority1->setProvinceId(110000);
        $priority1->setCityId(110100);
        $priority1->setDistrictId(110101);
        $priority1->setPriority(1);
        $repository->save($priority1);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot);
        $priority2->setDepotCode('FIND_BY_CODE_DEPOT');
        $priority2->setDepotName('Code Test Depot');
        $priority2->setProvinceId(110000);
        $priority2->setCityId(110100);
        $priority2->setDistrictId(110102);
        $priority2->setPriority(2);
        $repository->save($priority2);

        $results = $repository->findByDepotCode('FIND_BY_CODE_DEPOT');

        $this->assertCount(2, $results);
        $this->assertSame(1, $results[0]->getPriority());
        $this->assertSame(2, $results[1]->getPriority());
    }

    public function testFindByDepotId(): void
    {
        $repository = $this->getRepository();

        $results = $repository->findByDepotId('NON_EXISTENT_ID');

        $this->assertIsArray($results);
    }

    public function testFindByPriorityRange(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepotPriorities($repository);

        $depot = new Depot();
        $depot->setDepotCode('PRIORITY_RANGE_DEPOT');
        $depot->setDepotName('Priority Range Depot');
        $depot->setDepotAlias('Range Alias');
        $depot->setContact('Range Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Range Address');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);

        $priority1 = new DepotPriority();
        $priority1->setDepot($depot);
        $priority1->setDepotCode('PRIORITY_RANGE_DEPOT');
        $priority1->setDepotName('Priority Range Depot');
        $priority1->setProvinceId(110000);
        $priority1->setCityId(110100);
        $priority1->setDistrictId(110101);
        $priority1->setPriority(5);
        $repository->save($priority1);

        $priority2 = new DepotPriority();
        $priority2->setDepot($depot);
        $priority2->setDepotCode('PRIORITY_RANGE_DEPOT');
        $priority2->setDepotName('Priority Range Depot');
        $priority2->setProvinceId(110000);
        $priority2->setCityId(110100);
        $priority2->setDistrictId(110102);
        $priority2->setPriority(15);
        $repository->save($priority2);

        $priority3 = new DepotPriority();
        $priority3->setDepot($depot);
        $priority3->setDepotCode('PRIORITY_RANGE_DEPOT');
        $priority3->setDepotName('Priority Range Depot');
        $priority3->setProvinceId(110000);
        $priority3->setCityId(110100);
        $priority3->setDistrictId(110103);
        $priority3->setPriority(25);
        $repository->save($priority3);

        $results = $repository->findByPriorityRange(10, 20);

        $this->assertCount(1, $results);
        $this->assertSame(15, $results[0]->getPriority());
    }

    public function testFindByRegion(): void
    {
        $repository = $this->getRepository();

        $resultsProvince = $repository->findByRegion(110000);
        $this->assertIsArray($resultsProvince);

        $resultsCity = $repository->findByRegion(110000, 110100);
        $this->assertIsArray($resultsCity);

        $resultsDistrict = $repository->findByRegion(110000, 110100, 110101);
        $this->assertIsArray($resultsDistrict);
    }

    public function testFindActivePriorities(): void
    {
        $repository = $this->getRepository();

        $results = $repository->findActivePriorities();

        $this->assertIsArray($results);
    }

    public function testFindByDepotAndRegion(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepotPriorities($repository);

        $depot = new Depot();
        $depot->setDepotCode('REGION_SEARCH_DEPOT');
        $depot->setDepotName('Region Search Depot');
        $depot->setDepotAlias('Region Alias');
        $depot->setContact('Region Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Region Address');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        self::getEntityManager()->persist($depot);
        self::getEntityManager()->flush();

        $priority = new DepotPriority();
        $priority->setDepot($depot);
        $priority->setDepotId($depot->getId());
        $priority->setDepotCode('REGION_SEARCH_DEPOT');
        $priority->setDepotName('Region Search Depot');
        $priority->setProvinceId(110000);
        $priority->setCityId(110100);
        $priority->setDistrictId(110101);
        $priority->setPriority(1);
        $repository->save($priority);

        $result = $repository->findByDepotAndRegion(
            (string) $depot->getId(),
            110000,
            110100,
            110101
        );

        $this->assertNotNull($result);
        $this->assertInstanceOf(DepotPriority::class, $result);
        $this->assertSame((string) $depot->getId(), $result->getDepotId());
        $this->assertSame(110000, $result->getProvinceId());
        $this->assertSame(110100, $result->getCityId());
        $this->assertSame(110101, $result->getDistrictId());

        $notFound = $repository->findByDepotAndRegion(
            (string) $depot->getId(),
            999999,
            999999,
            999999
        );
        $this->assertNull($notFound);
    }
}
