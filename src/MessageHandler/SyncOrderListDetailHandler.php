<?php

namespace PinduoduoApiBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncOrderListDetailHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SyncOrderListDetailMessage $message): void
    {
        $item = $message->getOrderInfo();
        $orderSn = $item['order_sn'] ?? null;
        if (!is_string($orderSn) || '' === $orderSn) {
            return;
        }

        $order = $this->orderRepository->findOneBy(['orderSn' => $orderSn]);
        if (null === $order) {
            $order = new Order();
            $order->setOrderSn($orderSn);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
