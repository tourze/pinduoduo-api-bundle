<?php

namespace PinduoduoApiBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Goods::class)]
class GoodsListener
{
    public function __construct(
        private readonly SdkService $sdkService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 本地删除商品后，我们从远程也删除
     */
    public function postRemove(Goods $goods): void
    {
        if (!$goods->getMall()) {
            return;
        }
        $sdk = $this->sdkService->getMallSdk($goods->getMall(), ApplicationType::搬家上货);
        if ($sdk === null) {
            return;
        }

        $response = $sdk->auth_api->request('pdd.delete.goods.commit', [
            'goods_ids' => [$goods->getId()],
        ]);
        $this->logger->info('PDD删除商品', [
            'goods' => $goods,
            'response' => $response,
        ]);
    }
}
