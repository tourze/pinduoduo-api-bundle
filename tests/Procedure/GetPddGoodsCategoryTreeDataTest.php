<?php

namespace PinduoduoApiBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Param\GetPddGoodsCategoryTreeDataParam;
use PinduoduoApiBundle\Procedure\GetPddGoodsCategoryTreeData;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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
        $param = new GetPddGoodsCategoryTreeDataParam();
        $result = $procedure->execute($param);
        $this->assertIsArray($result->toArray());
        $this->assertNotEmpty($result->toArray());
        // 验证数组包含数字键（从category.json解析后的结果）
        $resultArray = $result->toArray();
        $this->assertIsNumeric(array_key_first($resultArray));
    }
}
