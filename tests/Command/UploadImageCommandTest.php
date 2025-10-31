<?php

namespace PinduoduoApiBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Command\UploadImageCommand;
use PinduoduoApiBundle\Exception\MallNotFoundException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(UploadImageCommand::class)]
#[RunTestsInSeparateProcesses]
final class UploadImageCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        /** @var UploadImageCommand $command */
        $command = self::getContainer()->get(UploadImageCommand::class);

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
        // 在没有有效商城数据的测试环境中，命令会抛出异常
        $this->expectException(MallNotFoundException::class);
        $this->expectExceptionMessage('找不到授权店铺');

        $this->commandTester->execute([
            'mallId' => '1',
            'url' => 'https://example.com/image.jpg',
        ]);
    }

    public function testArgumentMallId(): void
    {
        // 在没有有效商城数据的测试环境中，命令会抛出异常
        $this->expectException(MallNotFoundException::class);
        $this->expectExceptionMessage('找不到授权店铺');

        $this->commandTester->execute([
            'mallId' => '2',
            'url' => 'https://example.com/image.jpg',
        ]);
    }

    public function testArgumentUrl(): void
    {
        // 在没有有效商城数据的测试环境中，命令会抛出异常
        $this->expectException(MallNotFoundException::class);
        $this->expectExceptionMessage('找不到授权店铺');

        $this->commandTester->execute([
            'mallId' => '1',
            'url' => 'https://example.com/different-image.jpg',
        ]);
    }
}
