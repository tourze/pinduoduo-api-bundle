<?php

namespace PinduoduoApiBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\GetPddGoodsCategoryTreeData;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetPddGoodsCategoryTreeData::class)]
#[RunTestsInSeparateProcesses]
final class GetPddGoodsCategoryTreeDataTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeRetrievedFromContainer(): void
    {
        $procedure = self::getService(GetPddGoodsCategoryTreeData::class);
        $this->assertInstanceOf(GetPddGoodsCategoryTreeData::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(GetPddGoodsCategoryTreeData::class);
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    public function testExecuteReturnsArray(): void
    {
        $procedure = self::getService(GetPddGoodsCategoryTreeData::class);
        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        // 验证数组包含数字键（从category.json解析后的结果）
        $this->assertIsNumeric(array_key_first($result));
    }
}
