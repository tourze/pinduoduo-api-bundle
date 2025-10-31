<?php

namespace PinduoduoApiBundle\Tests\Procedure\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\Goods\GetPddGoodsMallSpecValue;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsMallSpecValue::class)]
#[RunTestsInSeparateProcesses]
final class GetPddGoodsMallSpecValueTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // Procedure 测试需要完整的数据库设置
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetPddGoodsMallSpecValue::class);
        $this->assertInstanceOf(GetPddGoodsMallSpecValue::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(GetPddGoodsMallSpecValue::class);
        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }

    public function testExecuteHasCorrectSignature(): void
    {
        $procedure = self::getService(GetPddGoodsMallSpecValue::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('array', $returnType->getName());
    }
}
