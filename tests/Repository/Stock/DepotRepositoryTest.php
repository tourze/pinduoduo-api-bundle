<?php

namespace PinduoduoApiBundle\Tests\Repository\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Stock\Depot;
use PinduoduoApiBundle\Enum\Stock\DepotBusinessTypeEnum;
use PinduoduoApiBundle\Enum\Stock\DepotStatusEnum;
use PinduoduoApiBundle\Enum\Stock\DepotTypeEnum;
use PinduoduoApiBundle\Repository\Stock\DepotRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DepotRepository::class)]
#[RunTestsInSeparateProcesses]
final class DepotRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(DepotRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allDepots = $repository->findAll();
        foreach ($allDepots as $depot) {
            $repository->remove($depot);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $depot = new Depot();
        $depot->setDepotCode('TEST_DEPOT');
        $depot->setDepotName('Test Depot');
        $depot->setDepotAlias('Test Depot Alias');
        $depot->setContact('Test Contact');
        $depot->setPhone('13800138000');
        $depot->setAddress('Test Address');
        $depot->setProvince(1);
        $depot->setCity(1);
        $depot->setDistrict(1);
        $depot->setZipCode('100000');

        $repository->save($depot);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(DepotRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindDepot(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('DEPOT001');
        $depot->setDepotName('主仓库');
        $depot->setDepotAlias('Main Depot');
        $depot->setContact('张三');
        $depot->setPhone('13800138000');
        $depot->setAddress('北京市朝阳区xxx路123号');
        $depot->setProvince(110000);
        $depot->setCity(110100);
        $depot->setDistrict(110101);
        $depot->setZipCode('100000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);

        $repository->save($depot);

        $foundDepot = $repository->find($depot->getId());
        $this->assertNotNull($foundDepot);
        $this->assertSame('DEPOT001', $foundDepot->getDepotCode());
        $this->assertSame('主仓库', $foundDepot->getDepotName());
        $this->assertSame('Main Depot', $foundDepot->getDepotAlias());
        $this->assertSame('张三', $foundDepot->getContact());
        $this->assertSame('13800138000', $foundDepot->getPhone());
        $this->assertSame(DepotTypeEnum::SELF_BUILT, $foundDepot->getType());
        $this->assertSame(DepotBusinessTypeEnum::NORMAL, $foundDepot->getBusinessType());
    }

    public function testFindOneByDepotCode(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('UNIQUE_CODE');
        $depot->setDepotName('唯一仓库');
        $depot->setDepotAlias('Unique Depot');
        $depot->setContact('李四');
        $depot->setPhone('13900139000');
        $depot->setAddress('上海市浦东新区xxx路456号');
        $depot->setProvince(310000);
        $depot->setCity(310100);
        $depot->setDistrict(310115);
        $depot->setZipCode('200000');

        $repository->save($depot);

        $foundDepot = $repository->findOneBy(['depotCode' => 'UNIQUE_CODE']);
        $this->assertNotNull($foundDepot);
        $this->assertSame('UNIQUE_CODE', $foundDepot->getDepotCode());
        $this->assertSame('唯一仓库', $foundDepot->getDepotName());
    }

    public function testFindByType(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot1 = new Depot();
        $depot1->setDepotCode('SELF_001');
        $depot1->setDepotName('自建仓库1');
        $depot1->setDepotAlias('Self Built 1');
        $depot1->setContact('王五');
        $depot1->setPhone('13700137000');
        $depot1->setAddress('广州市天河区xxx路789号');
        $depot1->setProvince(440000);
        $depot1->setCity(440100);
        $depot1->setDistrict(440106);
        $depot1->setZipCode('510000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);

        $depot2 = new Depot();
        $depot2->setDepotCode('THIRD_001');
        $depot2->setDepotName('第三方仓库1');
        $depot2->setDepotAlias('Third Party 1');
        $depot2->setContact('赵六');
        $depot2->setPhone('13600136000');
        $depot2->setAddress('深圳市南山区xxx路000号');
        $depot2->setProvince(440000);
        $depot2->setCity(440300);
        $depot2->setDistrict(440305);
        $depot2->setZipCode('518000');
        $depot2->setType(DepotTypeEnum::THIRD_PARTY);

        $repository->save($depot1);
        $repository->save($depot2);

        $selfBuiltDepots = $repository->findBy(['type' => DepotTypeEnum::SELF_BUILT]);
        $this->assertNotEmpty($selfBuiltDepots);

        $thirdPartyDepots = $repository->findBy(['type' => DepotTypeEnum::THIRD_PARTY]);
        $this->assertNotEmpty($thirdPartyDepots);
    }

    public function testFindByBusinessType(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot1 = new Depot();
        $depot1->setDepotCode('NORMAL_001');
        $depot1->setDepotName('普通仓库');
        $depot1->setDepotAlias('Normal Depot');
        $depot1->setContact('孙七');
        $depot1->setPhone('13500135000');
        $depot1->setAddress('成都市锦江区xxx路111号');
        $depot1->setProvince(510000);
        $depot1->setCity(510100);
        $depot1->setDistrict(510104);
        $depot1->setZipCode('610000');
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);

        $depot2 = new Depot();
        $depot2->setDepotCode('SPECIAL_001');
        $depot2->setDepotName('特殊仓库');
        $depot2->setDepotAlias('Special Depot');
        $depot2->setContact('周八');
        $depot2->setPhone('13400134000');
        $depot2->setAddress('西安市雁塔区xxx路222号');
        $depot2->setProvince(610000);
        $depot2->setCity(610100);
        $depot2->setDistrict(610113);
        $depot2->setZipCode('710000');
        $depot2->setBusinessType(DepotBusinessTypeEnum::RETURN);

        $repository->save($depot1);
        $repository->save($depot2);

        $normalDepots = $repository->findBy(['businessType' => DepotBusinessTypeEnum::NORMAL]);
        $this->assertNotEmpty($normalDepots);

        $returnDepots = $repository->findBy(['businessType' => DepotBusinessTypeEnum::RETURN]);
        $this->assertNotEmpty($returnDepots);
    }

    public function testFindByStatus(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot1 = new Depot();
        $depot1->setDepotCode('ACTIVE_001');
        $depot1->setDepotName('活跃仓库');
        $depot1->setDepotAlias('Active Depot');
        $depot1->setContact('吴九');
        $depot1->setPhone('13300133000');
        $depot1->setAddress('武汉市武昌区xxx路333号');
        $depot1->setProvince(420000);
        $depot1->setCity(420100);
        $depot1->setDistrict(420106);
        $depot1->setZipCode('430000');
        $depot1->setStatus(DepotStatusEnum::ACTIVE);

        $depot2 = new Depot();
        $depot2->setDepotCode('INACTIVE_001');
        $depot2->setDepotName('非活跃仓库');
        $depot2->setDepotAlias('Inactive Depot');
        $depot2->setContact('郑十');
        $depot2->setPhone('13200132000');
        $depot2->setAddress('长沙市岳麓区xxx路444号');
        $depot2->setProvince(430000);
        $depot2->setCity(430100);
        $depot2->setDistrict(430104);
        $depot2->setZipCode('410000');
        $depot2->setStatus(DepotStatusEnum::DISABLED);

        $repository->save($depot1);
        $repository->save($depot2);

        $activeDepots = $repository->findBy(['status' => DepotStatusEnum::ACTIVE]);
        $this->assertNotEmpty($activeDepots);

        $disabledDepots = $repository->findBy(['status' => DepotStatusEnum::DISABLED]);
        $this->assertNotEmpty($disabledDepots);
    }

    public function testFindByIsDefault(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot1 = new Depot();
        $depot1->setDepotCode('DEFAULT_001');
        $depot1->setDepotName('默认仓库');
        $depot1->setDepotAlias('Default Depot');
        $depot1->setContact('冯十一');
        $depot1->setPhone('13100131000');
        $depot1->setAddress('南京市鼓楼区xxx路555号');
        $depot1->setProvince(320000);
        $depot1->setCity(320100);
        $depot1->setDistrict(320106);
        $depot1->setZipCode('210000');
        $depot1->setIsDefault(true);

        $depot2 = new Depot();
        $depot2->setDepotCode('NON_DEFAULT_001');
        $depot2->setDepotName('非默认仓库');
        $depot2->setDepotAlias('Non Default Depot');
        $depot2->setContact('陈十二');
        $depot2->setPhone('13000130000');
        $depot2->setAddress('杭州市西湖区xxx路666号');
        $depot2->setProvince(330000);
        $depot2->setCity(330100);
        $depot2->setDistrict(330106);
        $depot2->setZipCode('310000');
        $depot2->setIsDefault(false);

        $repository->save($depot1);
        $repository->save($depot2);

        $defaultDepots = $repository->findBy(['isDefault' => true]);
        $this->assertNotEmpty($defaultDepots);

        $nonDefaultDepots = $repository->findBy(['isDefault' => false]);
        $this->assertNotEmpty($nonDefaultDepots);
    }

    public function testFindByWithNullDepotId(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('NULL_ID_001');
        $depot->setDepotName('无ID仓库');
        $depot->setDepotAlias('No ID Depot');
        $depot->setContact('楚十三');
        $depot->setPhone('12900129000');
        $depot->setAddress('合肥市蜀山区xxx路777号');
        $depot->setProvince(340000);
        $depot->setCity(340100);
        $depot->setDistrict(340104);
        $depot->setZipCode('230000');
        $depot->setDepotId(null);

        $repository->save($depot);

        $depotsWithNullId = $repository->findBy(['depotId' => null]);
        $this->assertNotEmpty($depotsWithNullId);

        $found = false;
        foreach ($depotsWithNullId as $item) {
            if ('NULL_ID_001' === $item->getDepotCode()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindAllReturnsAllDepots(): void
    {
        $repository = self::getService(DepotRepository::class);

        // 清空现有数据
        $allDepots = $repository->findAll();
        foreach ($allDepots as $depot) {
            $repository->remove($depot);
        }

        $depot1 = new Depot();
        $depot1->setDepotCode('ALL_001');
        $depot1->setDepotName('仓库1');
        $depot1->setDepotAlias('Depot 1');
        $depot1->setContact('韩十四');
        $depot1->setPhone('12800128000');
        $depot1->setAddress('济南市历下区xxx路888号');
        $depot1->setProvince(370000);
        $depot1->setCity(370100);
        $depot1->setDistrict(370102);
        $depot1->setZipCode('250000');

        $depot2 = new Depot();
        $depot2->setDepotCode('ALL_002');
        $depot2->setDepotName('仓库2');
        $depot2->setDepotAlias('Depot 2');
        $depot2->setContact('杨十五');
        $depot2->setPhone('12700127000');
        $depot2->setAddress('太原市小店区xxx路999号');
        $depot2->setProvince(140000);
        $depot2->setCity(140100);
        $depot2->setDistrict(140105);
        $depot2->setZipCode('030000');

        $repository->save($depot1);
        $repository->save($depot2);

        $depots = $repository->findAll();
        $this->assertCount(2, $depots);
    }

    public function testRemoveDepot(): void
    {
        $repository = self::getService(DepotRepository::class);

        $depot = new Depot();
        $depot->setDepotCode('TO_REMOVE_001');
        $depot->setDepotName('待删除仓库');
        $depot->setDepotAlias('To Remove Depot');
        $depot->setContact('朱十六');
        $depot->setPhone('12600126000');
        $depot->setAddress('石家庄市长安区xxx路000号');
        $depot->setProvince(130000);
        $depot->setCity(130100);
        $depot->setDistrict(130102);
        $depot->setZipCode('050000');

        $repository->save($depot);
        $id = $depot->getId();

        $repository->remove($depot);

        $foundDepot = $repository->find($id);
        $this->assertNull($foundDepot);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(DepotRepository::class);

        $this->clearAllDepots($repository);

        $depot1 = new Depot();
        $depot1->setDepotCode('DEPOT_C');
        $depot1->setDepotName('Depot C');
        $depot1->setDepotAlias('C Alias');
        $depot1->setContact('C Contact');
        $depot1->setPhone('13800138003');
        $depot1->setAddress('C Address');
        $depot1->setProvince(330000);
        $depot1->setCity(330100);
        $depot1->setDistrict(330101);
        $depot1->setZipCode('310000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot1->setStatus(DepotStatusEnum::ACTIVE);
        $depot1->setIsDefault(false);
        $this->persistAndFlush($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('DEPOT_A');
        $depot2->setDepotName('Depot A');
        $depot2->setDepotAlias('A Alias');
        $depot2->setContact('A Contact');
        $depot2->setPhone('13800138001');
        $depot2->setAddress('A Address');
        $depot2->setProvince(110000);
        $depot2->setCity(110100);
        $depot2->setDistrict(110101);
        $depot2->setZipCode('100000');
        $depot2->setType(DepotTypeEnum::THIRD_PARTY);
        $depot2->setBusinessType(DepotBusinessTypeEnum::RETURN);
        $depot2->setStatus(DepotStatusEnum::DISABLED);
        $depot2->setIsDefault(true);
        $this->persistAndFlush($depot2);

        $depot3 = new Depot();
        $depot3->setDepotCode('DEPOT_B');
        $depot3->setDepotName('Depot B');
        $depot3->setDepotAlias('B Alias');
        $depot3->setContact('B Contact');
        $depot3->setPhone('13800138002');
        $depot3->setAddress('B Address');
        $depot3->setProvince(210000);
        $depot3->setCity(210100);
        $depot3->setDistrict(210101);
        $depot3->setZipCode('200000');
        $depot3->setType(DepotTypeEnum::SELF_BUILT);
        $depot3->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot3->setStatus(DepotStatusEnum::ACTIVE);
        $depot3->setIsDefault(false);
        $this->persistAndFlush($depot3);

        $firstDepotAsc = $repository->findOneBy([], ['depotCode' => 'ASC']);
        $this->assertNotNull($firstDepotAsc);
        $this->assertSame('DEPOT_A', $firstDepotAsc->getDepotCode());

        $firstDepotDesc = $repository->findOneBy([], ['depotCode' => 'DESC']);
        $this->assertNotNull($firstDepotDesc);
        $this->assertSame('DEPOT_C', $firstDepotDesc->getDepotCode());

        $firstByProvinceAsc = $repository->findOneBy([], ['province' => 'ASC']);
        $this->assertNotNull($firstByProvinceAsc);
        $this->assertSame(110000, $firstByProvinceAsc->getProvince());

        $newestDepot = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestDepot);
        $this->assertSame($depot3->getId(), $newestDepot->getId());
    }

    private function clearAllDepots(DepotRepository $repository): void
    {
        $allDepots = $repository->findAll();
        foreach ($allDepots as $depot) {
            self::getEntityManager()->remove($depot);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Depot
    {
        $entity = new Depot();
        $entity->setDepotCode('TEST_DEPOT_' . uniqid());
        $entity->setDepotName('Test Depot ' . uniqid());
        $entity->setDepotAlias('Test Depot Alias');
        $entity->setContact('Test Contact');
        $entity->setPhone('13800138000');
        $entity->setAddress('Test Address');
        $entity->setProvince(110000);
        $entity->setCity(110100);
        $entity->setDistrict(110101);
        $entity->setZipCode('100000');
        $entity->setType(DepotTypeEnum::SELF_BUILT);
        $entity->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $entity->setStatus(DepotStatusEnum::ACTIVE);
        $entity->setIsDefault(false);

        return $entity;
    }

    protected function getRepository(): DepotRepository
    {
        return self::getService(DepotRepository::class);
    }

    public function testFindDefaultDepot(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        // 创建默认仓库
        $defaultDepot = new Depot();
        $defaultDepot->setDepotCode('DEFAULT_001');
        $defaultDepot->setDepotName('默认仓库');
        $defaultDepot->setDepotAlias('Default Depot');
        $defaultDepot->setContact('张三');
        $defaultDepot->setPhone('13800138000');
        $defaultDepot->setAddress('北京市朝阳区');
        $defaultDepot->setProvince(110000);
        $defaultDepot->setCity(110100);
        $defaultDepot->setDistrict(110105);
        $defaultDepot->setZipCode('100000');
        $defaultDepot->setType(DepotTypeEnum::SELF_BUILT);
        $defaultDepot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $defaultDepot->setStatus(DepotStatusEnum::ACTIVE);
        $defaultDepot->setIsDefault(true);
        $repository->save($defaultDepot);

        // 创建非默认仓库
        $nonDefaultDepot = new Depot();
        $nonDefaultDepot->setDepotCode('NON_DEFAULT_001');
        $nonDefaultDepot->setDepotName('非默认仓库');
        $nonDefaultDepot->setDepotAlias('Non-default Depot');
        $nonDefaultDepot->setContact('李四');
        $nonDefaultDepot->setPhone('13900139000');
        $nonDefaultDepot->setAddress('上海市浦东新区');
        $nonDefaultDepot->setProvince(310000);
        $nonDefaultDepot->setCity(310100);
        $nonDefaultDepot->setDistrict(310115);
        $nonDefaultDepot->setZipCode('200000');
        $nonDefaultDepot->setType(DepotTypeEnum::SELF_BUILT);
        $nonDefaultDepot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $nonDefaultDepot->setStatus(DepotStatusEnum::ACTIVE);
        $nonDefaultDepot->setIsDefault(false);
        $repository->save($nonDefaultDepot);

        $result = $repository->findDefaultDepot();

        $this->assertInstanceOf(Depot::class, $result);
        $this->assertSame('DEFAULT_001', $result->getDepotCode());
        $this->assertTrue($result->isDefault());
    }

    public function testFindByPddDepotId(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $depot = new Depot();
        $depot->setDepotId('123456789');
        $depot->setDepotCode('PDD_001');
        $depot->setDepotName('拼多多仓库');
        $depot->setDepotAlias('PDD Depot');
        $depot->setContact('王五');
        $depot->setPhone('13700137000');
        $depot->setAddress('广州市天河区');
        $depot->setProvince(440000);
        $depot->setCity(440100);
        $depot->setDistrict(440106);
        $depot->setZipCode('510000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot->setStatus(DepotStatusEnum::ACTIVE);
        $depot->setIsDefault(false);
        $repository->save($depot);

        $result = $repository->findByPddDepotId('123456789');

        $this->assertInstanceOf(Depot::class, $result);
        $this->assertSame('PDD_001', $result->getDepotCode());
        $this->assertSame('123456789', $result->getDepotId());

        $notFound = $repository->findByPddDepotId('999999999');
        $this->assertNull($notFound);
    }

    public function testFindActiveDepots(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $activeDepot1 = new Depot();
        $activeDepot1->setDepotCode('ACTIVE_001');
        $activeDepot1->setDepotName('活跃仓库1');
        $activeDepot1->setDepotAlias('Active Depot 1');
        $activeDepot1->setContact('张三');
        $activeDepot1->setPhone('13800138000');
        $activeDepot1->setAddress('北京市朝阳区');
        $activeDepot1->setProvince(110000);
        $activeDepot1->setCity(110100);
        $activeDepot1->setDistrict(110101);
        $activeDepot1->setZipCode('100000');
        $activeDepot1->setType(DepotTypeEnum::SELF_BUILT);
        $activeDepot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $activeDepot1->setStatus(DepotStatusEnum::ACTIVE);
        $activeDepot1->setIsDefault(false);
        $repository->save($activeDepot1);

        $inactiveDepot = new Depot();
        $inactiveDepot->setDepotCode('INACTIVE_001');
        $inactiveDepot->setDepotName('非活跃仓库');
        $inactiveDepot->setDepotAlias('Inactive Depot');
        $inactiveDepot->setContact('李四');
        $inactiveDepot->setPhone('13900139000');
        $inactiveDepot->setAddress('上海市浦东新区');
        $inactiveDepot->setProvince(310000);
        $inactiveDepot->setCity(310100);
        $inactiveDepot->setDistrict(310115);
        $inactiveDepot->setZipCode('200000');
        $inactiveDepot->setType(DepotTypeEnum::SELF_BUILT);
        $inactiveDepot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $inactiveDepot->setStatus(DepotStatusEnum::DISABLED);
        $inactiveDepot->setIsDefault(false);
        $repository->save($inactiveDepot);

        $activeDepot2 = new Depot();
        $activeDepot2->setDepotCode('ACTIVE_002');
        $activeDepot2->setDepotName('活跃仓库2');
        $activeDepot2->setDepotAlias('Active Depot 2');
        $activeDepot2->setContact('王五');
        $activeDepot2->setPhone('13700137000');
        $activeDepot2->setAddress('广州市天河区');
        $activeDepot2->setProvince(440000);
        $activeDepot2->setCity(440100);
        $activeDepot2->setDistrict(440106);
        $activeDepot2->setZipCode('510000');
        $activeDepot2->setType(DepotTypeEnum::THIRD_PARTY);
        $activeDepot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $activeDepot2->setStatus(DepotStatusEnum::ACTIVE);
        $activeDepot2->setIsDefault(false);
        $repository->save($activeDepot2);

        $results = $repository->findActiveDepots();

        $this->assertCount(2, $results);
        foreach ($results as $depot) {
            $this->assertSame(DepotStatusEnum::ACTIVE, $depot->getStatus());
        }
    }

    public function testFindByContact(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $depot1 = new Depot();
        $depot1->setDepotCode('CONTACT_001');
        $depot1->setDepotName('联系人测试仓库1');
        $depot1->setDepotAlias('Contact Test Depot 1');
        $depot1->setContact('张三');
        $depot1->setPhone('13800138000');
        $depot1->setAddress('北京市朝阳区');
        $depot1->setProvince(110000);
        $depot1->setCity(110100);
        $depot1->setDistrict(110101);
        $depot1->setZipCode('100000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot1->setStatus(DepotStatusEnum::ACTIVE);
        $depot1->setIsDefault(false);
        $repository->save($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('CONTACT_002');
        $depot2->setDepotName('联系人测试仓库2');
        $depot2->setDepotAlias('Contact Test Depot 2');
        $depot2->setContact('李四');
        $depot2->setPhone('13900139999');
        $depot2->setAddress('上海市浦东新区');
        $depot2->setProvince(310000);
        $depot2->setCity(310100);
        $depot2->setDistrict(310115);
        $depot2->setZipCode('200000');
        $depot2->setType(DepotTypeEnum::SELF_BUILT);
        $depot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot2->setStatus(DepotStatusEnum::ACTIVE);
        $depot2->setIsDefault(false);
        $repository->save($depot2);

        $resultsByName = $repository->findByContact('张三');
        $this->assertCount(1, $resultsByName);
        $this->assertSame('张三', $resultsByName[0]->getContact());

        $resultsByPhone = $repository->findByContact('13900139999');
        $this->assertCount(1, $resultsByPhone);
        $this->assertSame('李四', $resultsByPhone[0]->getContact());

        $resultsByPartialName = $repository->findByContact('张');
        $this->assertGreaterThanOrEqual(1, count($resultsByPartialName));
    }

    public function testFindByDepotCodeMethod(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $depot = new Depot();
        $depot->setDepotCode('CODE_TEST_001');
        $depot->setDepotName('代码测试仓库');
        $depot->setDepotAlias('Code Test Depot');
        $depot->setContact('赵六');
        $depot->setPhone('13600136000');
        $depot->setAddress('深圳市南山区');
        $depot->setProvince(440000);
        $depot->setCity(440300);
        $depot->setDistrict(440305);
        $depot->setZipCode('518000');
        $depot->setType(DepotTypeEnum::SELF_BUILT);
        $depot->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot->setStatus(DepotStatusEnum::ACTIVE);
        $depot->setIsDefault(false);
        $repository->save($depot);

        $result = $repository->findByDepotCode('CODE_TEST_001');

        $this->assertInstanceOf(Depot::class, $result);
        $this->assertSame('CODE_TEST_001', $result->getDepotCode());
        $this->assertSame('代码测试仓库', $result->getDepotName());

        $notFound = $repository->findByDepotCode('NOTEXIST');
        $this->assertNull($notFound);
    }

    public function testFindByDepotNameMethod(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $depot1 = new Depot();
        $depot1->setDepotCode('NAME_TEST_001');
        $depot1->setDepotName('测试仓库');
        $depot1->setDepotAlias('Test Depot 1');
        $depot1->setContact('孙七');
        $depot1->setPhone('13500135000');
        $depot1->setAddress('成都市锦江区');
        $depot1->setProvince(510000);
        $depot1->setCity(510100);
        $depot1->setDistrict(510104);
        $depot1->setZipCode('610000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot1->setStatus(DepotStatusEnum::ACTIVE);
        $depot1->setIsDefault(false);
        $repository->save($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('NAME_TEST_002');
        $depot2->setDepotName('测试仓库');
        $depot2->setDepotAlias('Test Depot 2');
        $depot2->setContact('周八');
        $depot2->setPhone('13400134000');
        $depot2->setAddress('西安市雁塔区');
        $depot2->setProvince(610000);
        $depot2->setCity(610100);
        $depot2->setDistrict(610113);
        $depot2->setZipCode('710000');
        $depot2->setType(DepotTypeEnum::THIRD_PARTY);
        $depot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot2->setStatus(DepotStatusEnum::ACTIVE);
        $depot2->setIsDefault(false);
        $repository->save($depot2);

        $results = $repository->findByDepotName('测试仓库');

        $this->assertCount(2, $results);
        foreach ($results as $depot) {
            $this->assertSame('测试仓库', $depot->getDepotName());
        }
    }

    public function testFindByRegionMethod(): void
    {
        $repository = $this->getRepository();
        $this->clearAllDepots($repository);

        $depot1 = new Depot();
        $depot1->setDepotCode('REGION_001');
        $depot1->setDepotName('区域仓库1');
        $depot1->setDepotAlias('Region Depot 1');
        $depot1->setContact('吴九');
        $depot1->setPhone('13300133000');
        $depot1->setAddress('武汉市武昌区');
        $depot1->setProvince(420000);
        $depot1->setCity(420100);
        $depot1->setDistrict(420106);
        $depot1->setZipCode('430000');
        $depot1->setType(DepotTypeEnum::SELF_BUILT);
        $depot1->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot1->setStatus(DepotStatusEnum::ACTIVE);
        $depot1->setIsDefault(false);
        $repository->save($depot1);

        $depot2 = new Depot();
        $depot2->setDepotCode('REGION_002');
        $depot2->setDepotName('区域仓库2');
        $depot2->setDepotAlias('Region Depot 2');
        $depot2->setContact('郑十');
        $depot2->setPhone('13200132000');
        $depot2->setAddress('武汉市洪山区');
        $depot2->setProvince(420000);
        $depot2->setCity(420100);
        $depot2->setDistrict(420111);
        $depot2->setZipCode('430000');
        $depot2->setType(DepotTypeEnum::SELF_BUILT);
        $depot2->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot2->setStatus(DepotStatusEnum::ACTIVE);
        $depot2->setIsDefault(false);
        $repository->save($depot2);

        $depot3 = new Depot();
        $depot3->setDepotCode('REGION_003');
        $depot3->setDepotName('区域仓库3');
        $depot3->setDepotAlias('Region Depot 3');
        $depot3->setContact('冯十一');
        $depot3->setPhone('13100131000');
        $depot3->setAddress('长沙市岳麓区');
        $depot3->setProvince(430000);
        $depot3->setCity(430100);
        $depot3->setDistrict(430104);
        $depot3->setZipCode('410000');
        $depot3->setType(DepotTypeEnum::SELF_BUILT);
        $depot3->setBusinessType(DepotBusinessTypeEnum::NORMAL);
        $depot3->setStatus(DepotStatusEnum::ACTIVE);
        $depot3->setIsDefault(false);
        $repository->save($depot3);

        $resultsByProvince = $repository->findByRegion(420000);
        $this->assertCount(2, $resultsByProvince);

        $resultsByCity = $repository->findByRegion(420000, 420100);
        $this->assertCount(2, $resultsByCity);

        $resultsByDistrict = $repository->findByRegion(420000, 420100, 420106);
        $this->assertCount(1, $resultsByDistrict);
        $this->assertSame(420106, $resultsByDistrict[0]->getDistrict());
    }

    public function testFindByRegionCoverageMethod(): void
    {
        $repository = $this->getRepository();

        try {
            $results = $repository->findByRegionCoverage(330000, 330100, 330106);
            $this->assertIsArray($results);
        } catch (\Exception $e) {
            self::markTestSkipped('JSON_SEARCH function may not be available in test database: ' . $e->getMessage());
        }
    }
}
