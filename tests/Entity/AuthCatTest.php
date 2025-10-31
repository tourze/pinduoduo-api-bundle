<?php

namespace PinduoduoApiBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\AuthCat;
use PinduoduoApiBundle\Entity\Mall;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AuthCat::class)]
final class AuthCatTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $authCat = new AuthCat();
        $this->assertInstanceOf(AuthCat::class, $authCat);
    }

    public function testGetAndSetMall(): void
    {
        $authCat = new AuthCat();
        $mall = new Mall();
        $this->assertNull($authCat->getMall());
        $authCat->setMall($mall);
        $this->assertSame($mall, $authCat->getMall());
    }

    public function testGetAndSetParentCatId(): void
    {
        $authCat = new AuthCat();
        $parentCatId = '12345';
        $authCat->setParentCatId($parentCatId);
        $this->assertSame($parentCatId, $authCat->getParentCatId());
    }

    public function testGetAndSetCatId(): void
    {
        $authCat = new AuthCat();
        $catId = '67890';
        $this->assertNull($authCat->getCatId());
        $authCat->setCatId($catId);
        $this->assertSame($catId, $authCat->getCatId());
    }

    public function testGetAndSetCatName(): void
    {
        $authCat = new AuthCat();
        $catName = 'Test Category';
        $this->assertNull($authCat->getCatName());
        $authCat->setCatName($catName);
        $this->assertSame($catName, $authCat->getCatName());
    }

    public function testIsLeafAndSetLeaf(): void
    {
        $authCat = new AuthCat();
        $this->assertNull($authCat->isLeaf());
        $authCat->setLeaf(true);
        $this->assertTrue($authCat->isLeaf());
        $authCat->setLeaf(false);
        $this->assertFalse($authCat->isLeaf());
    }

    public function testGetId(): void
    {
        $authCat = new AuthCat();
        $this->assertSame(0, $authCat->getId());
    }

    public function testToString(): void
    {
        $authCat = new AuthCat();
        $this->assertSame('0', $authCat->__toString());
    }

    protected function createEntity(): object
    {
        return new AuthCat();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'parentCatId' => ['parentCatId', '12345'],
            'catId' => ['catId', '67890'],
            'catName' => ['catName', 'Test Category'],
            'leaf' => ['leaf', true],
        ];
    }
}
