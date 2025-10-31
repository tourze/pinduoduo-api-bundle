<?php

namespace PinduoduoApiBundle\Tests\Repository\Goods;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Goods\Measurement;
use PinduoduoApiBundle\Repository\Goods\MeasurementRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(MeasurementRepository::class)]
#[RunTestsInSeparateProcesses]
final class MeasurementRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(MeasurementRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allMeasurements = $repository->findAll();
        foreach ($allMeasurements as $measurement) {
            $repository->remove($measurement);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $measurement = new Measurement();
        $measurement->setCode('TEST_UNIT');
        $measurement->setDescription('Test measurement unit');

        $repository->save($measurement);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindMeasurement(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $measurement = new Measurement();
        $measurement->setCode('kg');
        $measurement->setDescription('千克');

        $this->persistAndFlush($measurement);

        $foundMeasurement = $repository->find($measurement->getId());
        $this->assertNotNull($foundMeasurement);
        $this->assertSame('kg', $foundMeasurement->getCode());
        $this->assertSame('千克', $foundMeasurement->getDescription());
    }

    public function testFindOneByCode(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $measurement = new Measurement();
        $measurement->setCode('piece');
        $measurement->setDescription('件');

        $this->persistAndFlush($measurement);

        $foundMeasurement = $repository->findOneBy(['code' => 'piece']);
        $this->assertNotNull($foundMeasurement);
        $this->assertSame('piece', $foundMeasurement->getCode());
        $this->assertSame('件', $foundMeasurement->getDescription());
    }

    public function testUniqueCodeConstraint(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $measurement1 = new Measurement();
        $measurement1->setCode('unit');
        $measurement1->setDescription('单位1');

        $measurement2 = new Measurement();
        $measurement2->setCode('unit');
        $measurement2->setDescription('单位2');

        $repository->save($measurement1);

        $this->expectException(UniqueConstraintViolationException::class);
        $repository->save($measurement2);
    }

    public function testFindAllReturnsAllMeasurements(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        // 清空现有数据
        $allMeasurements = $repository->findAll();
        foreach ($allMeasurements as $measurement) {
            self::getEntityManager()->remove($measurement);
        }
        self::getEntityManager()->flush();

        $measurement1 = new Measurement();
        $measurement1->setCode('m');
        $measurement1->setDescription('米');

        $measurement2 = new Measurement();
        $measurement2->setCode('cm');
        $measurement2->setDescription('厘米');

        $repository->save($measurement1);
        $repository->save($measurement2);

        $measurements = $repository->findAll();
        $this->assertCount(2, $measurements);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        // 清理现有数据
        $allMeasurements = $repository->findAll();
        foreach ($allMeasurements as $measurement) {
            $repository->remove($measurement);
        }

        for ($i = 1; $i <= 5; ++$i) {
            $measurement = new Measurement();
            $measurement->setCode("unit_{$i}");
            $measurement->setDescription("单位 {$i}");
            $this->persistAndFlush($measurement);
        }

        $measurements = $repository->findBy([], ['code' => 'ASC'], 2, 1);
        $this->assertCount(2, $measurements);
        $this->assertSame('unit_2', $measurements[0]->getCode());
        $this->assertSame('unit_3', $measurements[1]->getCode());
    }

    public function testFindByWithNullDescription(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $measurement = new Measurement();
        $measurement->setCode('no_desc');
        $measurement->setDescription(null);

        $this->persistAndFlush($measurement);

        $measurementsWithNullDesc = $repository->findBy(['description' => null]);
        $this->assertNotEmpty($measurementsWithNullDesc);

        $found = false;
        foreach ($measurementsWithNullDesc as $item) {
            if ('no_desc' === $item->getCode()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRemoveMeasurement(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $measurement = new Measurement();
        $measurement->setCode('temp_code');
        $measurement->setDescription('临时单位');

        $this->persistAndFlush($measurement);
        $id = $measurement->getId();

        self::getEntityManager()->remove($measurement);
        self::getEntityManager()->flush();

        $foundMeasurement = $repository->find($id);
        $this->assertNull($foundMeasurement);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(MeasurementRepository::class);

        $this->clearAllMeasurements($repository);

        $measurement1 = new Measurement();
        $measurement1->setCode('code_a');
        $measurement1->setDescription('单位A');
        $this->persistAndFlush($measurement1);

        $measurement2 = new Measurement();
        $measurement2->setCode('code_b');
        $measurement2->setDescription('单位B');
        $this->persistAndFlush($measurement2);

        $firstMeasurementAsc = $repository->findOneBy([], ['code' => 'ASC']);
        $this->assertNotNull($firstMeasurementAsc);
        $this->assertSame('code_a', $firstMeasurementAsc->getCode());

        $firstMeasurementDesc = $repository->findOneBy([], ['code' => 'DESC']);
        $this->assertNotNull($firstMeasurementDesc);
        $this->assertSame('code_b', $firstMeasurementDesc->getCode());
    }

    private function clearAllMeasurements(MeasurementRepository $repository): void
    {
        $allMeasurements = $repository->findAll();
        foreach ($allMeasurements as $measurement) {
            self::getEntityManager()->remove($measurement);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Measurement
    {
        $entity = new Measurement();
        $entity->setCode('TEST_' . uniqid());
        $entity->setDescription('Test Measurement ' . uniqid());

        return $entity;
    }

    protected function getRepository(): MeasurementRepository
    {
        return self::getService(MeasurementRepository::class);
    }
}
