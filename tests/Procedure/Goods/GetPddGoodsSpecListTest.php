<?php

namespace PinduoduoApiBundle\Tests\Procedure\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\Goods\GetPddGoodsSpecList;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsSpecList::class)]
#[RunTestsInSeparateProcesses]
final class GetPddGoodsSpecListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // Procedure 测试需要完整的数据库设置
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetPddGoodsSpecList::class);
        $this->assertInstanceOf(GetPddGoodsSpecList::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(GetPddGoodsSpecList::class);
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    public function testExecuteHasCorrectSignature(): void
    {
        $procedure = self::getService(GetPddGoodsSpecList::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('array', $returnType->getName());
    }
}
