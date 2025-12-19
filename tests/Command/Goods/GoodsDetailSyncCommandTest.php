<?php

namespace PinduoduoApiBundle\Tests\Command\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\Goods\GoodsDetailSyncCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(GoodsDetailSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class GoodsDetailSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var GoodsDetailSyncCommand $command */
        $command = self::getContainer()->get(GoodsDetailSyncCommand::class);

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
        $exitCode = $this->commandTester->execute([
            'goodsId' => '12345',
        ]);

        // 期望返回 FAILURE 因为测试环境中没有该商品
        $this->assertSame(Command::FAILURE, $exitCode);
    }

    public function testArgumentGoodsId(): void
    {
        $exitCode = $this->commandTester->execute([
            'goodsId' => '67890',
        ]);

        // 期望返回 FAILURE 因为测试环境中没有该商品
        $this->assertSame(Command::FAILURE, $exitCode);
    }
}
