<?php

namespace PinduoduoApiBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Service\PinduoduoClient;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Goods::class)]
#[WithMonologChannel(channel: 'pinduoduo_api')]
class GoodsListener
{
    public function __construct(
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 本地删除商品后，我们从远程也删除
     */
    public function postRemove(Goods $goods): void
    {
        if (null === $goods->getMall()) {
            return;
        }

        try {
            $response = $this->pinduoduoClient->requestByMall($goods->getMall(), 'pdd.delete.goods.commit', [
                'goods_ids' => [$goods->getId()],
            ]);
        } catch (\Exception $e) {
            $this->logger->error('PDD删除商品失败', [
                'goods' => $goods,
                'error' => $e->getMessage(),
            ]);

            return;
        }
        $this->logger->info('PDD删除商品', [
            'goods' => $goods,
            'response' => $response,
        ]);
    }
}
