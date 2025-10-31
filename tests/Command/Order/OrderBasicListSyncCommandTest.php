<?php

namespace PinduoduoApiBundle\Tests\Command\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Order\OrderBasicListSyncCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(OrderBasicListSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class OrderBasicListSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var OrderBasicListSyncCommand $command */
        $command = self::getContainer()->get(OrderBasicListSyncCommand::class);

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
        $exitCode = $this->commandTester->execute([
            'mallId' => '1',
        ]);

        // 在没有有效商城数据的测试环境中，命令返回状态码 1
        $this->assertSame(Command::FAILURE, $exitCode);
    }

    public function testArgumentMallId(): void
    {
        $exitCode = $this->commandTester->execute([
            'mallId' => '2',
        ]);

        // 在没有有效商城数据的测试环境中，命令返回状态码 1
        $this->assertSame(Command::FAILURE, $exitCode);
    }
}
