<?php

namespace PinduoduoApiBundle\Command\Goods;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\SdkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.list.get
 */
#[AsCronTask('*/30 * * * *')]
#[AsCommand(name: GoodsSyncListCommand::NAME, description: '同步商品列表接口')]
class GoodsSyncListCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-mall-goods-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SdkService $sdkService,
        private readonly GoodsRepository $goodsRepository,
        private readonly SkuRepository $skuRepository,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mallId', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('mallId')) {
            $malls = $this->mallRepository->findBy(['id' => $input->getArgument('mallId')]);
        } else {
            $malls = $this->mallRepository->findAll();
        }

        foreach ($malls as $mall) {
            $page = 1;
            while (true) {
                $count = $this->syncList($mall, $page, $output);
                if (0 === $count) {
                    break;
                }
                ++$page;
            }
        }

        return Command::SUCCESS;
    }

    private function syncList(Mall $mall, int $page, OutputInterface $output): int
    {
        try {
            $response = $this->sdkService->request($mall, 'pdd.goods.list.get', [
                'page' => $page,
                'page_size' => 100,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('同步商品列表失败', [
                'exception' => $exception,
            ]);

            return 0;
        }

        foreach ($response['goods_list_get_response']['goods_list'] as $item) {
            $goods = $this->goodsRepository->findOneBy([
                'mall' => $mall,
                'id' => $item['goods_id'],
            ]);
            if (!$goods) {
                $goods = new Goods();
                $goods->setMall($mall);
                $goods->setId($item['goods_id']);
            }
            $goods->setMoreSku((bool) $item['is_more_sku']);
            $goods->setGoodsName($item['goods_name']);
            $goods->setThumbUrl($item['thumb_url']);
            $goods->setImageUrl($item['image_url']);
            $goods->setGoodsReserveQuantity($item['goods_reserve_quantity']);
            $goods->setGoodsQuantity($item['goods_quantity']);
            $goods->setOnsale((bool) $item['is_onsale']);
            $goods->setCreateTime(Carbon::createFromTimestamp($item['created_at'], date_default_timezone_get()));
            if (isset($item['sku_list']) && count($item['sku_list']) > 0) {
                $goods->setOuterGoodsId($item['sku_list'][0]['outer_goods_id']);
            }
            $this->entityManager->persist($goods);
            $this->entityManager->flush();
            $output->writeln("同步Goods：{$goods}");

            foreach ($item['sku_list'] as $subItem) {
                $sku = $this->skuRepository->findOneBy([
                    'goods' => $goods,
                    'id' => $subItem['sku_id'],
                ]);
                if (!$sku) {
                    $sku = new Sku();
                    $sku->setGoods($goods);
                    $sku->setId($subItem['sku_id']);
                }
                $sku->setOnsale((bool) $subItem['is_sku_onsale']);
                $sku->setReserveQuantity($subItem['reserve_quantity']);
                $sku->setOuterSkuId($subItem['outer_id']);
                $sku->setSpecName($subItem['spec']);
                $sku->setQuantity($subItem['sku_quantity']);
                $sku->setSpecDetails($subItem['spec_details']);
                $this->entityManager->persist($sku);
                $this->entityManager->flush();
                $output->writeln("同步Sku：{$sku}");

                $this->entityManager->detach($sku);
            }

            $this->entityManager->detach($goods);
        }

        return count($response['goods_list_get_response']['goods_list']);
    }
}
