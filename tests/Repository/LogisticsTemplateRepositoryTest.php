<?php

namespace PinduoduoApiBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsTemplateRepository::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsTemplateRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
        $repository = self::getService(LogisticsTemplateRepository::class);

        // 清理现有数据，避免 DataFixtures 检查失败
        $allLogisticsTemplates = $repository->findAll();
        foreach ($allLogisticsTemplates as $logisticsTemplate) {
            $repository->remove($logisticsTemplate);
        }

        // 创建关联的 Mall
        $mall = new Mall();
        $mall->setName('DataFixture Test Mall for LogisticsTemplate');
        $mall->setDescription('Test mall for logistics template data fixtures');

        // 添加一个测试数据以满足 DataFixtures 检查
        $logisticsTemplate = new LogisticsTemplate();
        $logisticsTemplate->setMall($mall);
        $logisticsTemplate->setCostType(CostType::ByAmount);
        $logisticsTemplate->setName('Test Logistics Template');

        $repository->save($logisticsTemplate);
    }

    public function testFindNonExistentEntityShouldReturnNull(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $result = $repository->find(999999);
        $this->assertNull($result);
    }

    public function testSaveAndFindLogisticsTemplate(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template = new LogisticsTemplate();
        $template->setMall($mall);
        $template->setName('标准运费模板');
        $template->setCostType(CostType::ByAmount);

        $repository->save($template);

        $foundTemplate = $repository->find($template->getId());
        $this->assertNotNull($foundTemplate);
        $this->assertSame('标准运费模板', $foundTemplate->getName());
        $this->assertSame(CostType::ByAmount, $foundTemplate->getCostType());
        $foundMall = $foundTemplate->getMall();
        $this->assertNotNull($foundMall);
        $this->assertSame($mall->getId(), $foundMall->getId());
    }

    public function testFindOneByName(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template = new LogisticsTemplate();
        $template->setMall($mall);
        $template->setName('包邮模板');
        $template->setCostType(CostType::ByWeight);

        $repository->save($template);

        $foundTemplate = $repository->findOneBy(['name' => '包邮模板']);
        $this->assertNotNull($foundTemplate);
        $this->assertSame('包邮模板', $foundTemplate->getName());
        $this->assertSame(CostType::ByWeight, $foundTemplate->getCostType());
    }

    public function testFindByMall(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall1 = new Mall();
        $mall1->setName('Mall 1');
        self::getEntityManager()->persist($mall1);

        $mall2 = new Mall();
        $mall2->setName('Mall 2');
        self::getEntityManager()->persist($mall2);

        $template1 = new LogisticsTemplate();
        $template1->setMall($mall1);
        $template1->setName('模板1');
        $template1->setCostType(CostType::ByAmount);

        $template2 = new LogisticsTemplate();
        $template2->setMall($mall1);
        $template2->setName('模板2');
        $template2->setCostType(CostType::ByWeight);

        $template3 = new LogisticsTemplate();
        $template3->setMall($mall2);
        $template3->setName('模板3');
        $template3->setCostType(CostType::ByAmount);

        $repository->save($template1);
        $repository->save($template2);
        $repository->save($template3);

        $mall1Templates = $repository->findBy(['mall' => $mall1]);
        $this->assertCount(2, $mall1Templates);

        $mall2Templates = $repository->findBy(['mall' => $mall2]);
        $this->assertCount(1, $mall2Templates);
    }

    public function testFindByCostType(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template1 = new LogisticsTemplate();
        $template1->setMall($mall);
        $template1->setName('按件计费模板');
        $template1->setCostType(CostType::ByAmount);

        $template2 = new LogisticsTemplate();
        $template2->setMall($mall);
        $template2->setName('按重量计费模板');
        $template2->setCostType(CostType::ByWeight);

        $repository->save($template1);
        $repository->save($template2);

        $byAmountTemplates = $repository->findBy(['costType' => CostType::ByAmount]);
        $this->assertNotEmpty($byAmountTemplates);

        $byWeightTemplates = $repository->findBy(['costType' => CostType::ByWeight]);
        $this->assertNotEmpty($byWeightTemplates);
    }

    public function testFindAllReturnsAllTemplates(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        // 清空现有数据
        $allTemplates = $repository->findAll();
        foreach ($allTemplates as $template) {
            $repository->remove($template);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template1 = new LogisticsTemplate();
        $template1->setMall($mall);
        $template1->setName('模板1');
        $template1->setCostType(CostType::ByAmount);

        $template2 = new LogisticsTemplate();
        $template2->setMall($mall);
        $template2->setName('模板2');
        $template2->setCostType(CostType::ByWeight);

        $repository->save($template1);
        $repository->save($template2);

        $templates = $repository->findAll();
        $this->assertCount(2, $templates);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        // 清理现有数据
        $allLogisticsTemplates = $repository->findAll();
        foreach ($allLogisticsTemplates as $logisticsTemplate) {
            $repository->remove($logisticsTemplate);
        }

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        for ($i = 1; $i <= 5; ++$i) {
            $template = new LogisticsTemplate();
            $template->setMall($mall);
            $template->setName("模板 {$i}");
            $template->setCostType(0 === $i % 2 ? CostType::ByWeight : CostType::ByAmount);
            $repository->save($template);
        }

        $templates = $repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $templates);
        $this->assertSame('模板 2', $templates[0]->getName());
        $this->assertSame('模板 3', $templates[1]->getName());
    }

    public function testFindByCostTypeDifferentValues(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        $mall->setMerchantType(MerchantType::企业);
        $mall->setMallCharacter(MallCharacter::NEITHER);
        self::getEntityManager()->persist($mall);

        // 创建按件计费的模板
        $templateByAmount = new LogisticsTemplate();
        $templateByAmount->setMall($mall);
        $templateByAmount->setName('按件计费模板');
        $templateByAmount->setCostType(CostType::ByAmount);

        // 创建按重量计费的模板
        $templateByWeight = new LogisticsTemplate();
        $templateByWeight->setMall($mall);
        $templateByWeight->setName('按重量计费模板');
        $templateByWeight->setCostType(CostType::ByWeight);

        self::getEntityManager()->persist($templateByAmount);
        self::getEntityManager()->persist($templateByWeight);
        self::getEntityManager()->flush();

        // 测试查询按件计费的模板
        $byAmountTemplates = $repository->findBy(['costType' => CostType::ByAmount]);
        $this->assertNotEmpty($byAmountTemplates);

        $found = false;
        foreach ($byAmountTemplates as $item) {
            if ('按件计费模板' === $item->getName()) {
                $found = true;
                $this->assertSame(CostType::ByAmount, $item->getCostType());
                break;
            }
        }
        $this->assertTrue($found, '应该找到按件计费的模板');
    }

    public function testRemoveLogisticsTemplate(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template = new LogisticsTemplate();
        $template->setMall($mall);
        $template->setName('待删除模板');
        $template->setCostType(CostType::ByAmount);

        $repository->save($template);
        $id = $template->getId();

        $repository->remove($template);

        $foundTemplate = $repository->find($id);
        $this->assertNull($foundTemplate);
    }

    public function testFindOneByOrderBy(): void
    {
        $repository = self::getService(LogisticsTemplateRepository::class);

        $this->clearAllTemplates($repository);

        $mall = new Mall();
        $mall->setName('Test Mall');
        self::getEntityManager()->persist($mall);

        $template1 = new LogisticsTemplate();
        $template1->setMall($mall);
        $template1->setName('Template C');
        $template1->setCostType(CostType::ByWeight);
        $this->persistAndFlush($template1);

        $template2 = new LogisticsTemplate();
        $template2->setMall($mall);
        $template2->setName('Template A');
        $template2->setCostType(CostType::ByAmount);
        $this->persistAndFlush($template2);

        $template3 = new LogisticsTemplate();
        $template3->setMall($mall);
        $template3->setName('Template B');
        $template3->setCostType(CostType::ByWeight);
        $this->persistAndFlush($template3);

        $firstTemplateAsc = $repository->findOneBy([], ['name' => 'ASC']);
        $this->assertNotNull($firstTemplateAsc);
        $this->assertSame('Template A', $firstTemplateAsc->getName());

        $firstTemplateDesc = $repository->findOneBy([], ['name' => 'DESC']);
        $this->assertNotNull($firstTemplateDesc);
        $this->assertSame('Template C', $firstTemplateDesc->getName());

        $newestTemplate = $repository->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($newestTemplate);
        $this->assertSame($template3->getId(), $newestTemplate->getId());
    }

    private function clearAllTemplates(LogisticsTemplateRepository $repository): void
    {
        $allTemplates = $repository->findAll();
        foreach ($allTemplates as $template) {
            self::getEntityManager()->remove($template);
        }
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): LogisticsTemplate
    {
        $mall = new Mall();
        $mall->setName('Test Mall for LogisticsTemplate ' . uniqid());
        self::getEntityManager()->persist($mall);

        $entity = new LogisticsTemplate();
        $entity->setMall($mall);
        $entity->setName('Test LogisticsTemplate ' . uniqid());
        $entity->setCostType(CostType::ByAmount);

        return $entity;
    }

    protected function getRepository(): LogisticsTemplateRepository
    {
        return self::getService(LogisticsTemplateRepository::class);
    }
}
