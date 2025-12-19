<?php

namespace PinduoduoApiBundle\Tests\Procedure\Img;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\Img\UploadPddMallImage;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(UploadPddMallImage::class)]
#[RunTestsInSeparateProcesses]
final class UploadPddMallImageTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeRetrievedFromContainer(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);
        $this->assertInstanceOf(UploadPddMallImage::class, $procedure);
    }

    public function testProcedureImplementsCorrectInterface(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    public function testProcedureHasRequiredDependencies(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);

        // 验证 Procedure 有必要的依赖注入
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasProperty('mallRepository'));
        $this->assertTrue($reflection->hasProperty('uploadService'));
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
