<?php

namespace PinduoduoApiBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\ExecPddApi;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(ExecPddApi::class)]
#[RunTestsInSeparateProcesses]
final class ExecPddApiTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeRetrievedFromContainer(): void
    {
        $procedure = self::getService(ExecPddApi::class);
        $this->assertInstanceOf(ExecPddApi::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(ExecPddApi::class);
        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(ExecPddApi::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
