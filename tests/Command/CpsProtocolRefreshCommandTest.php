<?php

namespace PinduoduoApiBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\CpsProtocolRefreshCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(CpsProtocolRefreshCommand::class)]
#[RunTestsInSeparateProcesses]
final class CpsProtocolRefreshCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var CpsProtocolRefreshCommand $command */
        $command = self::getContainer()->get(CpsProtocolRefreshCommand::class);

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

    public function testArgumentForce(): void
    {
        $exitCode = $this->commandTester->execute(['force' => '1']);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
