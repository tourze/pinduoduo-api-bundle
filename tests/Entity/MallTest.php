<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Mall::class)]
final class MallTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $mall = new Mall();
        $this->assertInstanceOf(Mall::class, $mall);
    }

    public function testGetAndSetName(): void
    {
        $mall = new Mall();
        $name = 'Test Mall';
        $mall->setName($name);
        $this->assertSame($name, $mall->getName());
    }

    public function testGetAndSetDescription(): void
    {
        $mall = new Mall();
        $description = 'Test mall description';
        $this->assertNull($mall->getDescription());
        $mall->setDescription($description);
        $this->assertSame($description, $mall->getDescription());
    }

    public function testGetAndSetLogo(): void
    {
        $mall = new Mall();
        $logo = 'https://example.com/logo.png';
        $this->assertNull($mall->getLogo());
        $mall->setLogo($logo);
        $this->assertSame($logo, $mall->getLogo());
    }

    public function testGetAndSetMerchantType(): void
    {
        $mall = new Mall();
        $merchantType = MerchantType::企业;
        $this->assertNull($mall->getMerchantType());
        $mall->setMerchantType($merchantType);
        $this->assertSame($merchantType, $mall->getMerchantType());
    }

    public function testGetAndSetMallCharacter(): void
    {
        $mall = new Mall();
        $mallCharacter = MallCharacter::NEITHER;
        $this->assertNull($mall->getMallCharacter());
        $mall->setMallCharacter($mallCharacter);
        $this->assertSame($mallCharacter, $mall->getMallCharacter());
    }

    public function testCollectionsInitialization(): void
    {
        $mall = new Mall();
        $this->assertCount(0, $mall->getAuthLogs());
        $this->assertCount(0, $mall->getLogisticsTemplates());
        $this->assertCount(0, $mall->getVideos());
        $this->assertCount(0, $mall->getAuthCats());
    }

    public function testToString(): void
    {
        $mall = new Mall();
        $this->assertSame('', $mall->__toString());
    }

    protected function createEntity(): object
    {
        return new Mall();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'name' => ['name', '测试店铺'],
            'description' => ['description', '这是一个测试店铺的描述'],
            'logo' => ['logo', 'https://example.com/logo.png'],
            'cpsProtocolStatus' => ['cpsProtocolStatus', true],
        ];
    }
}
