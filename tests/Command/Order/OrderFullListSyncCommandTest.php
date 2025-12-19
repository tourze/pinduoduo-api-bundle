<?php

namespace PinduoduoApiBundle\Tests\Command\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Order\OrderFullListSyncCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(OrderFullListSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class OrderFullListSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var OrderFullListSyncCommand $command */
        $command = self::getContainer()->get(OrderFullListSyncCommand::class);

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
        $exitCode = $this->commandTester->execute([]);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testArgumentMallId(): void
    {
        $exitCode = $this->commandTester->execute(['mallId' => '1']);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testArgumentDate(): void
    {
        $exitCode = $this->commandTester->execute(['date' => '2023-01-01']);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
