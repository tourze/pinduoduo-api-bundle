<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\CostType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsTemplate::class)]
final class LogisticsTemplateTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $template = new LogisticsTemplate();
        $this->assertInstanceOf(LogisticsTemplate::class, $template);
    }

    public function testGetAndSetMall(): void
    {
        $template = new LogisticsTemplate();
        $mall = new Mall();
        $this->assertNull($template->getMall());
        $template->setMall($mall);
        $this->assertSame($mall, $template->getMall());
    }

    public function testGetAndSetCostType(): void
    {
        $template = new LogisticsTemplate();
        $costType = CostType::ByWeight;
        $this->assertNull($template->getCostType());
        $template->setCostType($costType);
        $this->assertSame($costType, $template->getCostType());
    }

    public function testGetAndSetName(): void
    {
        $template = new LogisticsTemplate();
        $name = 'Standard Shipping Template';
        $this->assertNull($template->getName());
        $template->setName($name);
        $this->assertSame($name, $template->getName());
    }

    public function testToString(): void
    {
        $template = new LogisticsTemplate();
        $this->assertSame('', $template->__toString());
    }

    protected function createEntity(): object
    {
        return new LogisticsTemplate();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'name' => ['name', 'Standard Shipping Template'],
        ];
    }
}
