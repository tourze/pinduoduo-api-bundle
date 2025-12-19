<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Exception\ErrorMessageConstants;

#[CoversClass(ErrorMessageConstants::class)]
final class ErrorMessageConstantsTest extends TestCase
{
    public function testMallNotFoundConstant(): void
    {
        self::assertSame('找不到店铺信息', ErrorMessageConstants::MALL_NOT_FOUND);
    }

    public function testCategoryNotFoundConstant(): void
    {
        self::assertSame('找不到分类信息', ErrorMessageConstants::CATEGORY_NOT_FOUND);
    }

    public function testDirectoryNotFoundConstant(): void
    {
        self::assertSame('找不到目录', ErrorMessageConstants::DIRECTORY_NOT_FOUND);
    }

    public function testCannotBeInstantiated(): void
    {
        $reflection = new \ReflectionClass(ErrorMessageConstants::class);
        $constructor = $reflection->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue($constructor->isPrivate());
    }
}
