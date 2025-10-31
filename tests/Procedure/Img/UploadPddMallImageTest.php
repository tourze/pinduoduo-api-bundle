<?php

namespace PinduoduoApiBundle\Tests\Procedure\Img;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PinduoduoApiBundle\Procedure\Img\UploadPddMallImage;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

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

    public function testProcedureHasRequiredProperties(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);
        $this->assertObjectHasProperty('mallId', $procedure);
        $this->assertObjectHasProperty('imgUrl', $procedure);

        // 验证属性类型
        $reflection = new \ReflectionClass($procedure);
        $mallIdProperty = $reflection->getProperty('mallId');
        $imgUrlProperty = $reflection->getProperty('imgUrl');

        $this->assertTrue($mallIdProperty->hasType());
        $this->assertEquals('string', (string) $mallIdProperty->getType());
        $this->assertTrue($imgUrlProperty->hasType());
        $this->assertEquals('string', (string) $imgUrlProperty->getType());
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(UploadPddMallImage::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
