<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use PinduoduoApiBundle\Repository\MallRepository;
use PinduoduoApiBundle\Service\PinduoduoClient;
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
#[AsCronTask(expression: '*/30 * * * *')]
#[AsCommand(name: self::NAME, description: '同步商品列表接口')]
#[WithMonologChannel(channel: 'pinduoduo_api')]
class GoodsSyncListCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-mall-goods-list';

    public function __construct(
        private readonly MallRepository $mallRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PinduoduoClient $pinduoduoClient,
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
        if (null !== $input->getArgument('mallId')) {
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
        $response = $this->fetchGoodsListPage($mall, $page);
        if (null === $response) {
            return 0;
        }

        $goodsListResponse = $response['goods_list_get_response'] ?? null;
        if (!is_array($goodsListResponse)) {
            return 0;
        }

        $goodsList = $goodsListResponse['goods_list'] ?? [];
        assert(is_array($goodsList));

        foreach ($goodsList as $item) {
            if (!is_array($item)) {
                continue;
            }
            $this->processGoodsItem($mall, $item, $output);
        }

        return count($goodsList);
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchGoodsListPage(Mall $mall, int $page): ?array
    {
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.list.get', [
                'page' => $page,
                'page_size' => 100,
            ]);

            // 检查是否有数据
            if (!isset($response['goods_list'])) {
                return null;
            }

            return ['goods_list_get_response' => $response];
        } catch (\Throwable $exception) {
            $this->logger->error('同步商品列表失败', [
                'exception' => $exception,
            ]);

            return null;
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function processGoodsItem(Mall $mall, array $item, OutputInterface $output): void
    {
        $goods = $this->findOrCreateGoods($mall, $item);
        $this->updateGoodsData($goods, $item);

        $this->entityManager->persist($goods);
        $this->entityManager->flush();
        $output->writeln("同步Goods：{$goods}");

        $skuList = $item['sku_list'] ?? [];
        assert(is_array($skuList));
        $this->processSkuList($goods, $skuList, $output);
        $this->entityManager->detach($goods);
    }

    /**
     * @param array<mixed> $item
     */
    private function findOrCreateGoods(Mall $mall, array $item): Goods
    {
        $goods = $this->goodsRepository->findOneBy([
            'mall' => $mall,
            'id' => $item['goods_id'],
        ]);

        if (!$goods instanceof Goods) {
            $goods = new Goods();
            $goods->setMall($mall);
        }

        return $goods;
    }

    /**
     * @param array<mixed> $item
     */
    private function updateGoodsData(Goods $goods, array $item): void
    {
        $goods->setMoreSku((bool) ($item['is_more_sku'] ?? false));
        $goods->setGoodsName($this->extractStringOrNull($item, 'goods_name'));
        $goods->setThumbUrl($this->extractStringOrNull($item, 'thumb_url'));
        $goods->setImageUrl($this->extractStringOrNull($item, 'image_url'));
        $goods->setGoodsReserveQuantity($this->extractIntOrNull($item, 'goods_reserve_quantity'));
        $goods->setGoodsQuantity($this->extractIntOrNull($item, 'goods_quantity'));
        $goods->setOnsale((bool) ($item['is_onsale'] ?? false));

        $this->updateGoodsCreatedAt($goods, $item);
        $this->updateGoodsOuterId($goods, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateGoodsCreatedAt(Goods $goods, array $item): void
    {
        $createdAt = $item['created_at'] ?? null;
        if (!is_int($createdAt)) {
            $goods->setCreateTime(null);

            return;
        }

        $createTime = \DateTimeImmutable::createFromFormat('U', (string) $createdAt);
        $goods->setCreateTime(false !== $createTime ? $createTime : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function updateGoodsOuterId(Goods $goods, array $item): void
    {
        $skuList = $item['sku_list'] ?? null;
        if (!is_array($skuList) || 0 === count($skuList)) {
            return;
        }

        $firstSku = $skuList[0] ?? null;
        if (!is_array($firstSku)) {
            return;
        }

        $outerGoodsId = $firstSku['outer_goods_id'] ?? null;
        $goods->setOuterGoodsId(is_string($outerGoodsId) ? $outerGoodsId : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function extractStringOrNull(array $item, string $key): ?string
    {
        $value = $item[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<mixed> $item
     */
    private function extractIntOrNull(array $item, string $key): ?int
    {
        $value = $item[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param array<mixed> $skuList
     */
    private function processSkuList(Goods $goods, array $skuList, OutputInterface $output): void
    {
        foreach ($skuList as $subItem) {
            if (!is_array($subItem)) {
                continue;
            }
            $this->processSkuItem($goods, $subItem, $output);
        }
    }

    /**
     * @param array<mixed> $subItem
     */
    private function processSkuItem(Goods $goods, array $subItem, OutputInterface $output): void
    {
        $sku = $this->findOrCreateSku($goods, $subItem);
        $this->updateSkuData($sku, $subItem);

        $this->entityManager->persist($sku);
        $this->entityManager->flush();
        $output->writeln("同步Sku：{$sku}");

        $this->entityManager->detach($sku);
    }

    /**
     * @param array<mixed> $subItem
     */
    private function findOrCreateSku(Goods $goods, array $subItem): Sku
    {
        $skuId = $subItem['sku_id'] ?? null;

        $sku = $this->skuRepository->findOneBy([
            'goods' => $goods,
            'id' => $skuId,
        ]);

        if (!$sku instanceof Sku) {
            $sku = new Sku();
            $sku->setGoods($goods);
            if (is_int($skuId)) {
                $sku->setId((string) $skuId);
            } elseif (is_string($skuId)) {
                $sku->setId($skuId);
            }
        }

        return $sku;
    }

    /**
     * @param array<mixed> $subItem
     */
    private function updateSkuData(Sku $sku, array $subItem): void
    {
        $sku->setOnsale((bool) ($subItem['is_sku_onsale'] ?? false));
        $sku->setReserveQuantity(is_int($subItem['reserve_quantity'] ?? null) ? $subItem['reserve_quantity'] : null);
        $sku->setOuterSkuId(is_string($subItem['outer_id'] ?? null) ? $subItem['outer_id'] : null);
        $sku->setSpecName(is_string($subItem['spec'] ?? null) ? $subItem['spec'] : null);
        $sku->setQuantity(is_int($subItem['sku_quantity'] ?? null) ? $subItem['sku_quantity'] : null);
        $specDetails = $subItem['spec_details'] ?? null;
        if (is_array($specDetails)) {
            /** @var array<string, mixed> $validSpecDetails */
            $validSpecDetails = [];
            foreach ($specDetails as $key => $value) {
                $validSpecDetails[(string) $key] = $value;
            }
            $sku->setSpecDetails($validSpecDetails);
        } else {
            $sku->setSpecDetails(null);
        }
    }
}
