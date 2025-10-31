<?php

namespace PinduoduoApiBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\GetPddLogisticsTemplateList;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

/**
 * @internal
 */
#[CoversClass(GetPddLogisticsTemplateList::class)]
#[RunTestsInSeparateProcesses]
final class GetPddLogisticsTemplateListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeRetrievedFromContainer(): void
    {
        $procedure = self::getService(GetPddLogisticsTemplateList::class);
        $this->assertInstanceOf(GetPddLogisticsTemplateList::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(GetPddLogisticsTemplateList::class);
        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }

    public function testExecuteHasCorrectSignature(): void
    {
        $procedure = self::getService(GetPddLogisticsTemplateList::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('array', $returnType->getName());
    }
}
