<?php

namespace PinduoduoApiBundle\Tests\Entity\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PinduoduoApiBundle\Entity\Order\Order;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Order::class)]
final class OrderTest extends AbstractEntityTestCase
{
    public function testCanCreateInstance(): void
    {
        $order = new Order();
        $this->assertInstanceOf(Order::class, $order);
    }

    public function testToString(): void
    {
        $order = new Order();
        $this->assertSame('', $order->__toString());
    }

    protected function createEntity(): object
    {
        return new Order();
    }

    public static function propertiesProvider(): iterable
    {
        yield from [
            'orderSn' => ['orderSn', 'ORDER20241201001'],
            'addressEncrypted' => ['addressEncrypted', '加密地址信息'],
            'addressMask' => ['addressMask', '打码地址信息'],
            'buyerMemo' => ['buyerMemo', '买家留言'],
            'bondedWarehouse' => ['bondedWarehouse', '保税仓A'],
            'capitalFreeDiscount' => ['capitalFreeDiscount', '10.00'],
            'confirmTime' => ['confirmTime', new \DateTimeImmutable()],
            'supportNationwideWarranty' => ['supportNationwideWarranty', true],
            'freeSf' => ['freeSf', true],
            'discountAmount' => ['discountAmount', 5.50],
            'platformDiscount' => ['platformDiscount', 2.50],
            'returnFreightPayer' => ['returnFreightPayer', true],
            'lastShipTime' => ['lastShipTime', new \DateTimeImmutable('+3 days')],
            'deliveryOneDay' => ['deliveryOneDay', true],
            'cardInfoList' => ['cardInfoList', ['card1', 'card2']],
            'stockOut' => ['stockOut', false],
            'receiveTime' => ['receiveTime', new \DateTimeImmutable('+7 days')],
            'payTime' => ['payTime', new \DateTimeImmutable('-1 day')],
            'giftList' => ['giftList', ['gift1', 'gift2']],
            'invoiceStatus' => ['invoiceStatus', true],
            'serviceFeeDetail' => ['serviceFeeDetail', ['fee' => 1.00]],
            'orderTagList' => ['orderTagList', ['tag1', 'tag2']],
            'luckyFlag' => ['luckyFlag', false],
            'shippingType' => ['shippingType', 1],
            'remark' => ['remark', '订单备注'],
            'orderChangeAmount' => ['orderChangeAmount', 1.50],
            'onlySupportReplace' => ['onlySupportReplace', true],
            'trackingNumber' => ['trackingNumber', 'SF123456789'],
            'duoduoWholesale' => ['duoduoWholesale', false],
            'preSale' => ['preSale', false],
            'shippingTime' => ['shippingTime', new \DateTimeImmutable()],
            'goodsAmount' => ['goodsAmount', 100.00],
            'payAmount' => ['payAmount', 95.50],
            'sellerDiscount' => ['sellerDiscount', 3.00],
            'postage' => ['postage', 8.00],
            'itemList' => ['itemList', ['item1', 'item2']],
            'context' => ['context', ['key' => 'value']],
        ];
    }
}
