<?php

namespace PinduoduoApiBundle\Tests\Command\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Goods\GoodsSyncListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsSyncListCommand::class)]
#[RunTestsInSeparateProcesses]
final class GoodsSyncListCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var GoodsSyncListCommand $command */
        $command = self::getContainer()->get(GoodsSyncListCommand::class);

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
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testArgumentMallId(): void
    {
        $exitCode = $this->commandTester->execute(['mallId' => '1']);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
