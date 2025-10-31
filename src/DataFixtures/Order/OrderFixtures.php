<?php

namespace PinduoduoApiBundle\DataFixtures\Order;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Enum\Order\ConfirmStatus;
use PinduoduoApiBundle\Enum\Order\OrderStatus;
use PinduoduoApiBundle\Enum\Order\PayType;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试订单数据
        for ($i = 1; $i <= 5; ++$i) {
            $order = new Order();
            $order->setOrderSn('TEST-ORDER-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT));
            $order->setPayAmount(99.99 + $i);
            $order->setGoodsAmount(89.99 + $i);
            $order->setOrderStatus(OrderStatus::cases()[($i - 1) % 3]);
            $order->setConfirmStatus(ConfirmStatus::cases()[($i - 1) % 3]);
            $order->setPayType(PayType::cases()[($i - 1) % count(PayType::cases())]);
            $order->setStockOut(0 === $i % 2);
            $order->setPreSale(0 === $i % 3);
            $order->setPayTime(new \DateTimeImmutable('-' . $i . ' days'));
            $order->setTrackingNumber('TRACK' . str_pad((string) $i, 10, '0', STR_PAD_LEFT));
            $order->setBuyerMemo('测试买家留言 ' . $i);
            $order->setRemark('测试订单备注 ' . $i);

            $manager->persist($order);
        }

        $manager->flush();
    }
}
