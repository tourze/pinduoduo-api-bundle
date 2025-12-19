<?php

namespace PinduoduoApiBundle\Tests\Procedure\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\Goods\GetPddMallCatRule;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetPddMallCatRule::class)]
#[RunTestsInSeparateProcesses]
final class GetPddMallCatRuleTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // Procedure 测试需要完整的数据库设置
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetPddMallCatRule::class);
        $this->assertInstanceOf(GetPddMallCatRule::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(GetPddMallCatRule::class);
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetPddMallCatRule::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
