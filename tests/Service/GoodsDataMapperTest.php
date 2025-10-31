<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use PinduoduoApiBundle\Enum\Goods\GoodsType;
use PinduoduoApiBundle\Service\GoodsDataMapper;

/**
 * @internal
 */
#[CoversClass(GoodsDataMapper::class)]
final class GoodsDataMapperTest extends TestCase
{
    private GoodsDataMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new GoodsDataMapper();
    }

    public function testMapBasicFieldsWithValidData(): void
    {
        $goods = new Goods();
        $response = [
            'goods_name' => 'Test Product',
            'outer_goods_id' => 'OUTER123',
            'image_url' => 'https://example.com/image.jpg',
            'market_price' => 10000,
            'is_folt' => true,
            'delivery_type' => 1,
            'goods_type' => 1,
            'pre_sale_time' => 1672531200,
        ];

        $this->mapper->mapBasicFields($goods, $response);

        self::assertSame('Test Product', $goods->getGoodsName());
        self::assertSame('OUTER123', $goods->getOuterGoodsId());
        self::assertSame('https://example.com/image.jpg', $goods->getImageUrl());
        self::assertSame(10000, $goods->getMarketPrice());
        self::assertTrue($goods->isFolt());
        self::assertInstanceOf(DeliveryType::class, $goods->getDeliveryType());
        self::assertInstanceOf(GoodsType::class, $goods->getGoodsType());
    }

    public function testMapBasicFieldsWithNullValues(): void
    {
        $goods = new Goods();
        $response = [];

        $this->mapper->mapBasicFields($goods, $response);

        self::assertNull($goods->getGoodsName());
        self::assertNull($goods->getOuterGoodsId());
        self::assertNull($goods->getImageUrl());
        self::assertNull($goods->getMarketPrice());
    }

    public function testMapBasicFieldsWithArrayFields(): void
    {
        $goods = new Goods();
        $response = [
            'elec_goods_attributes' => ['key' => 'value'],
            'goods_property_list' => [['prop_id' => 1]],
            'video_gallery' => ['video1.mp4'],
        ];

        $this->mapper->mapBasicFields($goods, $response);

        self::assertSame(['key' => 'value'], $goods->getElecGoodsAttributes());
        self::assertSame([['prop_id' => 1]], $goods->getGoodsProperties());
        self::assertSame(['video1.mp4'], $goods->getVideoGallery());
    }

    public function testMapBasicFieldsWithBooleanDefaults(): void
    {
        $goods = new Goods();
        $response = [
            'delivery_one_day' => false,
            'quan_guo_lian_bao' => false,
            'invoice_status' => false,
        ];

        $this->mapper->mapBasicFields($goods, $response);

        self::assertFalse($goods->isDeliveryOneDay());
        self::assertFalse($goods->isQuanGuoLianBao());
        self::assertFalse($goods->isInvoiceStatus());
    }
}
