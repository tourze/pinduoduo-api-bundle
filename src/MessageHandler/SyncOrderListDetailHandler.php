<?php

namespace PinduoduoApiBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Order\Order;
use PinduoduoApiBundle\Message\SyncOrderListDetailMessage;
use PinduoduoApiBundle\Repository\Order\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncOrderListDetailHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function __invoke(SyncOrderListDetailMessage $message): void
    {
        $item = $message->getOrderInfo();
        $order = $this->orderRepository->findOneBy(['orderSn' => $item['order_sn']]);
        if ($order === null) {
            $order = new Order();
            $order->setOrderSn($item['order_sn']);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
