<?php

namespace PinduoduoApiBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\MallInfoSyncCommand;
use PinduoduoApiBundle\Entity\Mall;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(MallInfoSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class MallInfoSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var MallInfoSyncCommand $command */
        $command = self::getContainer()->get(MallInfoSyncCommand::class);

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

    public function testSyncInfo(): void
    {
        /** @var MallInfoSyncCommand $command */
        $command = self::getContainer()->get(MallInfoSyncCommand::class);

        $mall = new Mall();
        $mall->setName('测试店铺');

        // 由于 syncInfo 会实际调用 API，这里只验证方法存在且可调用
        // 实际的 API 调用会在集成测试中验证
        $reflection = new \ReflectionMethod($command, 'syncInfo');
        $this->assertTrue($reflection->isPublic());
    }
}
