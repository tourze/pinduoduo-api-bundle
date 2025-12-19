<?php

namespace PinduoduoApiBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\GetPddMallAuthorizationCates;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetPddMallAuthorizationCates::class)]
#[RunTestsInSeparateProcesses]
final class GetPddMallAuthorizationCatesTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // Procedure 测试需要完整的数据库设置
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetPddMallAuthorizationCates::class);
        $this->assertInstanceOf(GetPddMallAuthorizationCates::class, $procedure);
    }

    public function testExecuteHasCorrectSignature(): void
    {
        $procedure = self::getService(GetPddMallAuthorizationCates::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame(ArrayResult::class, $returnType->getName());
    }
}
