<?php

namespace PinduoduoApiBundle\Tests\Command\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Goods\CategoryLoopSyncCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(CategoryLoopSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class CategoryLoopSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var CategoryLoopSyncCommand $command */
        $command = self::getContainer()->get(CategoryLoopSyncCommand::class);

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    public function testExecuteCommand(): void
    {
        $exitCode = $this->commandTester->execute([]);

        // 期望返回 FAILURE 因为测试环境中没有账号
        $this->assertSame(Command::FAILURE, $exitCode);
    }

    public function testArgumentParentId(): void
    {
        $exitCode = $this->commandTester->execute(['parentId' => '1']);

        // 期望返回 FAILURE 因为测试环境中没有账号
        $this->assertSame(Command::FAILURE, $exitCode);
    }
}
