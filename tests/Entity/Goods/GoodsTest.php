<?php

namespace PinduoduoApiBundle\Tests\Entity\Goods;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Goods\Goods;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Goods::class)]
final class GoodsTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $goods = new Goods();
        $this->assertInstanceOf(Goods::class, $goods);
    }

    public function testToString(): void
    {
        $goods = new Goods();
        $this->assertSame('', $goods->__toString());
    }

    protected function createEntity(): object
    {
        return new Goods();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'outerGoodsId' => ['outerGoodsId', 'TEST_GOODS_001'],
            'goodsSn' => ['goodsSn', 'SKU123456'],
            'goodsName' => ['goodsName', '测试商品名称'],
            'goodsQuantity' => ['goodsQuantity', 100],
            'imageUrl' => ['imageUrl', 'https://example.com/image.jpg'],
            'onsale' => ['onsale', true],
            'secondHand' => ['secondHand', false],
            'shipmentLimitSecond' => ['shipmentLimitSecond', 86400],
            'groupRequiredCustomerNum' => ['groupRequiredCustomerNum', 2],
            'goodsReserveQuantity' => ['goodsReserveQuantity', 10],
            'moreSku' => ['moreSku', true],
            'badFruitClaim' => ['badFruitClaim', 1],
            'buyLimit' => ['buyLimit', 5],
            'carouselGalleryList' => ['carouselGalleryList', ['image1.jpg', 'image2.jpg']],
            'thumbUrl' => ['thumbUrl', 'https://example.com/thumb.jpg'],
            'maiJiaZiTi' => ['maiJiaZiTi', 'TEMPLATE_ID_001'],
            'twoPiecesDiscount' => ['twoPiecesDiscount', 95],
            'customerNum' => ['customerNum', '1000'],
            'elecGoodsAttributes' => ['elecGoodsAttributes', ['type' => 'card']],
            'folt' => ['folt', true],
            'goodsProperties' => ['goodsProperties', ['color' => 'red', 'size' => 'L']],
            'videoGallery' => ['videoGallery', ['video1.mp4']],
            'zhiHuanBuXiu' => ['zhiHuanBuXiu', 365],
            'deliveryOneDay' => ['deliveryOneDay', true],
            'overseaType' => ['overseaType', 1],
            'warmTips' => ['warmTips', '温馨提示内容'],
            'goodsDesc' => ['goodsDesc', '商品描述'],
            'warehouse' => ['warehouse', '保税仓A'],
            'outSourceType' => ['outSourceType', 1],
            'goodsTravelAttr' => ['goodsTravelAttr', ['travel' => 'yes']],
            'quanGuoLianBao' => ['quanGuoLianBao', true],
            'marketPrice' => ['marketPrice', 99900],
            'orderLimit' => ['orderLimit', 3],
            'invoiceStatus' => ['invoiceStatus', true],
            'groupPreSale' => ['groupPreSale', false],
            'preSaleTime' => ['preSaleTime', new \DateTimeImmutable('+7 days')],
            'detailGalleryList' => ['detailGalleryList', ['detail1.jpg', 'detail2.jpg']],
            'skuPreSale' => ['skuPreSale', false],
            'tinyName' => ['tinyName', '短标题'],
            'preSale' => ['preSale', false],
            'outSourceGoodsId' => ['outSourceGoodsId', 'EXTERNAL_001'],
            'goodsTradeAttr' => ['goodsTradeAttr', ['trade' => 'online']],
            'lackOfWeightClaim' => ['lackOfWeightClaim', true],
            'overseaGoods' => ['overseaGoods', ['oversea' => 'yes']],
        ];
    }
}
