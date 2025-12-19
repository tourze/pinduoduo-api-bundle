<?php

namespace PinduoduoApiBundle\Tests\Command\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Goods\CategoryRuleGetCommand;
use PinduoduoApiBundle\Entity\Mall;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(CategoryRuleGetCommand::class)]
#[RunTestsInSeparateProcesses]
final class CategoryRuleGetCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var CategoryRuleGetCommand $command */
        $command = self::getContainer()->get(CategoryRuleGetCommand::class);

        $application = new Application();
        $application->addCommand($command);

        $this->commandTester = new CommandTester($command);
    }

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    public function testExecuteCommand(): void
    {
        // 创建测试用的 Mall 实体
        $mall = new Mall();
        $mall->setName('测试店铺');
        $mall->setDescription('测试描述');
        $mall->setCreateTime(new \DateTimeImmutable());
        $mall->setUpdateTime(new \DateTimeImmutable());

        // 持久化到数据库
        $em = self::getEntityManager();
        $em->persist($mall);
        $em->flush();

        $exitCode = $this->commandTester->execute([
            'mallId' => (string) $mall->getId(),
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testArgumentMallId(): void
    {
        // 创建测试用的 Mall 实体
        $mall = new Mall();
        $mall->setName('测试店铺');
        $mall->setDescription('测试描述');
        $mall->setCreateTime(new \DateTimeImmutable());
        $mall->setUpdateTime(new \DateTimeImmutable());

        // 持久化到数据库
        $em = self::getEntityManager();
        $em->persist($mall);
        $em->flush();

        $exitCode = $this->commandTester->execute([
            'mallId' => (string) $mall->getId(),
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testArgumentCategoryId(): void
    {
        // 创建测试用的 Mall 实体
        $mall = new Mall();
        $mall->setName('测试店铺');
        $mall->setDescription('测试描述');
        $mall->setCreateTime(new \DateTimeImmutable());
        $mall->setUpdateTime(new \DateTimeImmutable());

        // 持久化到数据库
        $em = self::getEntityManager();
        $em->persist($mall);
        $em->flush();

        $exitCode = $this->commandTester->execute([
            'mallId' => (string) $mall->getId(),
            'categoryId' => '1',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
