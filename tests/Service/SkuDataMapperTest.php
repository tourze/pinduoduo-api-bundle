<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Service\SkuDataMapper;

/**
 * @internal
 */
#[CoversClass(SkuDataMapper::class)]
final class SkuDataMapperTest extends TestCase
{
    private SkuDataMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SkuDataMapper();
    }

    public function testMapFieldsWithValidData(): void
    {
        $sku = new Sku();
        $item = [
            'out_sku_sn' => 'SKU123',
            'thumb_url' => 'https://example.com/thumb.jpg',
            'multi_price' => 5000,
            'quantity' => 100,
            'reserve_quantity' => 10,
            'is_onsale' => true,
            'price' => 9900,
        ];

        $this->mapper->mapFields($sku, $item);

        self::assertSame('SKU123', $sku->getOutSkuSn());
        self::assertSame('https://example.com/thumb.jpg', $sku->getThumbUrl());
        self::assertSame(5000, $sku->getMultiPrice());
        self::assertSame(100, $sku->getQuantity());
        self::assertSame(10, $sku->getReserveQuantity());
        self::assertTrue($sku->isOnsale());
        self::assertSame(9900, $sku->getPrice());
    }

    public function testMapFieldsWithNullValues(): void
    {
        $sku = new Sku();
        $item = [];

        $this->mapper->mapFields($sku, $item);

        self::assertNull($sku->getOutSkuSn());
        self::assertNull($sku->getThumbUrl());
        self::assertNull($sku->getMultiPrice());
        self::assertNull($sku->getQuantity());
        self::assertFalse($sku->isOnsale());
    }

    public function testMapFieldsWithArrayData(): void
    {
        $sku = new Sku();
        $item = [
            'oversea_sku' => ['country' => 'US'],
            'spec' => ['size' => 'L', 'color' => 'Red'],
            'sku_property_list' => [['prop_id' => 1, 'value' => 'test']],
        ];

        $this->mapper->mapFields($sku, $item);

        self::assertSame(['country' => 'US'], $sku->getOverseaSku());
        self::assertSame(['size' => 'L', 'color' => 'Red'], $sku->getSpecDetails());
        self::assertSame([['prop_id' => 1, 'value' => 'test']], $sku->getSkuProperties());
    }

    public function testMapFieldsWithPreSaleTime(): void
    {
        $sku = new Sku();
        $item = [
            'sku_pre_sale_time' => 1672531200,
        ];

        $this->mapper->mapFields($sku, $item);

        self::assertInstanceOf(\DateTimeImmutable::class, $sku->getPreSaleTime());
    }

    public function testMapFieldsWithZeroPreSaleTime(): void
    {
        $sku = new Sku();
        $item = [
            'sku_pre_sale_time' => 0,
        ];

        $this->mapper->mapFields($sku, $item);

        self::assertNull($sku->getPreSaleTime());
    }
}
