<?php

namespace PinduoduoApiBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Enum\ApplicationType;

class ApplicationTypeTest extends TestCase
{
    public function testGetLabel_allCases_returnsCorrectLabels(): void
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
    
    public function testCases_returnsAllEnumCases(): void
    {
        $cases = ApplicationType::cases();
        
        $this->assertCount(12, $cases);
        $this->assertContainsOnlyInstancesOf(ApplicationType::class, $cases);
    }
    
    public function testEnumValues_areConsistent(): void
    {
        // 验证枚举值和名称一致性
        foreach (ApplicationType::cases() as $case) {
            $this->assertEquals($case->name, $case->value);
            $this->assertEquals($case->name, $case->getLabel());
        }
    }
} 