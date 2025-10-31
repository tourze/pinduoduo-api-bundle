<?php

namespace PinduoduoApiBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Enum\ApplicationType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ApplicationType::class)]
final class ApplicationTypeTest extends AbstractEnumTestCase
{
    public function testGetLabelAllCasesReturnsCorrectLabels(): void
    {
        $this->assertEquals('推广优化', ApplicationType::推广优化->getLabel());
        $this->assertEquals('短信服务', ApplicationType::短信服务->getLabel());
        $this->assertEquals('打单', ApplicationType::打单->getLabel());
        $this->assertEquals('进销存', ApplicationType::进销存->getLabel());
        $this->assertEquals('商品优化分析', ApplicationType::商品优化分析->getLabel());
        $this->assertEquals('搬家上货', ApplicationType::搬家上货->getLabel());
        $this->assertEquals('电子面单', ApplicationType::电子面单->getLabel());
        $this->assertEquals('企业ERP', ApplicationType::企业ERP->getLabel());
        $this->assertEquals('仓储管理系统', ApplicationType::仓储管理系统->getLabel());
        $this->assertEquals('订单处理', ApplicationType::订单处理->getLabel());
        $this->assertEquals('快团团', ApplicationType::快团团->getLabel());
        $this->assertEquals('跨境企业ERP报关版', ApplicationType::跨境企业ERP报关版->getLabel());
    }

    public function testCasesReturnsAllEnumCases(): void
    {
        $cases = ApplicationType::cases();

        $this->assertCount(12, $cases);
        // 验证所有case都是有效的ApplicationType实例
        foreach ($cases as $case) {
            $this->assertInstanceOf(ApplicationType::class, $case);
        }
    }

    public function testEnumValuesAreConsistent(): void
    {
        // 验证枚举值和名称一致性
        foreach (ApplicationType::cases() as $case) {
            $this->assertEquals($case->name, $case->value);
            $this->assertEquals($case->name, $case->getLabel());
        }
    }

    public function testToArray(): void
    {
        $result = ApplicationType::推广优化->toArray();
        // 验证返回结果的结构和内容
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
