<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\MallRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(MallRepository::class)]
#[RunTestsInSeparateProcesses]
final class MallRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(MallRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allMalls = $repository->findAll();
        foreach ($allMalls as $mall) {
            $this->assertInstanceOf(Mall::class, $mall);
            $repository->remove($mall);
        }

        // 添加一个测试数据以满足 DataFixtures 检查
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall');
        $mall->setDescription('Test mall description for data fixtures');
        $mall->setMerchantType(MerchantType::企业);
        $mall->setMallCharacter(MallCharacter::NEITHER);
        $mall->setCpsProtocolStatus(true);
        $repository->save($mall);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(MallRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindMall(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall = new Mall();
        $mall->setName('测试商城');
        $mall->setDescription('这是一个测试商城');
        $mall->setLogo('https://example.com/logo.png');

        $repository->save($mall);

        $foundMall = $repository->find($mall->getId());
        $this->assertNotNull($foundMall);
        $this->assertSame('测试商城', $foundMall->getName());
        $this->assertSame('这是一个测试商城', $foundMall->getDescription());
        $this->assertSame('https://example.com/logo.png', $foundMall->getLogo());
    }

    public function testFindOneByName(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall = new Mall();
        $mall->setName('唯一商城名称');
        $mall->setDescription('唯一商城描述');

        $repository->save($mall);

        $foundMall = $repository->findOneBy(['name' => '唯一商城名称']);
        $this->assertNotNull($foundMall);
        $this->assertSame('唯一商城名称', $foundMall->getName());
        $this->assertSame('唯一商城描述', $foundMall->getDescription());
    }

    public function testFindByMerchantType(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall1 = new Mall();
        $mall1->setName('个人商城');
        $mall1->setMerchantType(MerchantType::个人);

        $mall2 = new Mall();
        $mall2->setName('企业商城');
        $mall2->setMerchantType(MerchantType::企业);

        $repository->save($mall1);
        $repository->save($mall2);

        $personalMalls = $repository->findBy(['merchantType' => MerchantType::个人]);
        $this->assertNotEmpty($personalMalls);

        $enterpriseMalls = $repository->findBy(['merchantType' => MerchantType::企业]);
        $this->assertNotEmpty($enterpriseMalls);
    }

    public function testFindByMallCharacter(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall1 = new Mall();
        $mall1->setName('普通商城');
        $mall1->setMallCharacter(MallCharacter::NEITHER);

        $mall2 = new Mall();
        $mall2->setName('品牌商城');
        $mall2->setMallCharacter(MallCharacter::MANUFACTURER);

        $repository->save($mall1);
        $repository->save($mall2);

        $normalMalls = $repository->findBy(['mallCharacter' => MallCharacter::NEITHER]);
        $this->assertNotEmpty($normalMalls);

        $brandMalls = $repository->findBy(['mallCharacter' => MallCharacter::MANUFACTURER]);
        $this->assertNotEmpty($brandMalls);
    }

    public function testFindAllReturnsAllMalls(): void
    {
        $repository = self::getService(MallRepository::class);

        // 清空现有数据
        $allMalls = $repository->findAll();
        foreach ($allMalls as $mall) {
            $this->assertInstanceOf(Mall::class, $mall);
            $repository->remove($mall);
        }

        $mall1 = new Mall();
        $mall1->setName('商城1');

        $mall2 = new Mall();
        $mall2->setName('商城2');

        $repository->save($mall1);
        $repository->save($mall2);

        $malls = $repository->findAll();
        $this->assertCount(2, $malls);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(MallRepository::class);

        // 清理现有数据
        $allMalls = $repository->findAll();
        foreach ($allMalls as $mall) {
            $this->assertInstanceOf(Mall::class, $mall);
            $repository->remove($mall);
        }

        for ($i = 1; $i <= 5; ++$i) {
            $mall = new Mall();
            $mall->setName("商城 {$i}");
            $repository->save($mall);
        }

        $malls = $repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $malls);
        $this->assertSame('商城 2', $malls[0]->getName());
        $this->assertSame('商城 3', $malls[1]->getName());
    }

    public function testFindByWithNullFields(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall = new Mall();
        $mall->setName('空字段商城');
        $mall->setDescription(null);
        $mall->setLogo(null);
        $mall->setMerchantType(null);
        $mall->setMallCharacter(null);
        $mall->setCpsProtocolStatus(null);

        $repository->save($mall);

        $mallsWithNullDescription = $repository->findBy(['description' => null]);
        $this->assertNotEmpty($mallsWithNullDescription);

        $mallsWithNullLogo = $repository->findBy(['logo' => null]);
        $this->assertNotEmpty($mallsWithNullLogo);

        $mallsWithNullMerchantType = $repository->findBy(['merchantType' => null]);
        $this->assertNotEmpty($mallsWithNullMerchantType);

        $mallsWithNullMallCharacter = $repository->findBy(['mallCharacter' => null]);
        $this->assertNotEmpty($mallsWithNullMallCharacter);

        $mallsWithNullCpsProtocol = $repository->findBy(['cpsProtocolStatus' => null]);
        $this->assertNotEmpty($mallsWithNullCpsProtocol);
    }

    public function testCpsProtocolStatus(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall1 = new Mall();
        $mall1->setName('已签协议商城');
        $mall1->setCpsProtocolStatus(true);

        $mall2 = new Mall();
        $mall2->setName('未签协议商城');
        $mall2->setCpsProtocolStatus(false);

        $repository->save($mall1);
        $repository->save($mall2);

        $signedMalls = $repository->findBy(['cpsProtocolStatus' => true]);
        $this->assertNotEmpty($signedMalls);

        $unsignedMalls = $repository->findBy(['cpsProtocolStatus' => false]);
        $this->assertNotEmpty($unsignedMalls);
    }

    public function testRemoveMall(): void
    {
        $repository = self::getService(MallRepository::class);

        $mall = new Mall();
        $mall->setName('待删除商城');

        $repository->save($mall);
        $id = $mall->getId();

        $repository->remove($mall);

        $foundMall = $repository->find($id);
        $this->assertNull($foundMall);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(MallRepository::class);

        $this->clearAllMalls($repository);

        $mall1 = new Mall();
        $mall1->setName('Mall C');
        $mall1->setDescription('Description C');
        $this->persistAndFlush($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall A');
        $mall2->setDescription('Description A');
        $this->persistAndFlush($mall2);

        $mall3 = new Mall();
        $mall3->setName('Mall B');
        $mall3->setDescription('Description B');
        $this->persistAndFlush($mall3);

        $firstMallAsc = $repository->findOneBy([], ['name' => 'ASC']);
        $this->assertNotNull($firstMallAsc);
        $this->assertSame('Mall A', $firstMallAsc->getName());

        $firstMallDesc = $repository->findOneBy([], ['name' => 'DESC']);
        $this->assertNotNull($firstMallDesc);
        $this->assertSame('Mall C', $firstMallDesc->getName());

        $newestMall = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestMall);
        $this->assertSame($mall3->getId(), $newestMall->getId());
    }

    private function clearAllMalls(MallRepository $repository): void
    {
        $allMalls = $repository->findAll();
        foreach ($allMalls as $mall) {
            self::getEntityManager()->remove($mall);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): Mall
    {
        $entity = new Mall();
        $entity->setName('Test Mall ' . uniqid());
        $entity->setDescription('Test Mall Description ' . uniqid());
        $entity->setMerchantType(MerchantType::企业);
        $entity->setMallCharacter(MallCharacter::NEITHER);
        $entity->setCpsProtocolStatus(true);

        return $entity;
    }

    protected function getRepository(): MallRepository
    {
        return self::getService(MallRepository::class);
    }
}
