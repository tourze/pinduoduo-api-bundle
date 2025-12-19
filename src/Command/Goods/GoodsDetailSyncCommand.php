<?php

namespace PinduoduoApiBundle\Command\Goods;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Country;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Entity\Goods\Sku;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Repository\CountryRepository;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use PinduoduoApiBundle\Repository\Goods\GoodsRepository;
use PinduoduoApiBundle\Repository\Goods\SkuRepository;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use PinduoduoApiBundle\Service\GoodsDataMapper;
use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Service\SkuDataMapper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.commit.detail.get
 */
#[AsCommand(name: self::NAME, description: '同步商品详情')]
final class GoodsDetailSyncCommand extends LockableCommand
{
    public const NAME = 'pdd:sync-goods-detail';

    public function __construct(
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly GoodsRepository $goodsRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CountryRepository $countryRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly LogisticsTemplateRepository $templateRepository,
        private readonly SkuRepository $skuRepository,
        private readonly GoodsDataMapper $goodsDataMapper,
        private readonly SkuDataMapper $skuDataMapper,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('goodsId', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $goods = $this->goodsRepository->find($input->getArgument('goodsId'));
        if (!$goods instanceof Goods) {
            return Command::FAILURE;
        }

        $response = $this->fetchGoodsDetail($goods);
        if (null === $response) {
            return Command::FAILURE;
        }

        $this->updateGoodsFromResponse($goods, $response);
        $skuList = $response['sku_list'] ?? [];
        assert(is_array($skuList));
        $this->updateSkuListFromResponse($goods, $skuList);

        return Command::SUCCESS;
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchGoodsDetail(Goods $goods): ?array
    {
        $mall = $goods->getMall();
        if (null === $mall) {
            return null;
        }

        try {
            return $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.detail.get', [
                'goods_id' => $goods->getId(),
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array<mixed> $response
     */
    private function updateGoodsFromResponse(Goods $goods, array $response): void
    {
        $this->updateGoodsBasicFields($goods, $response);
        $this->updateGoodsRelations($goods, $response);

        $this->entityManager->persist($goods);
        $this->entityManager->flush();
    }

    /**
     * @param array<mixed> $response
     */
    private function updateGoodsBasicFields(Goods $goods, array $response): void
    {
        $this->goodsDataMapper->mapBasicFields($goods, $response);
    }

    /**
     * @param array<mixed> $response
     */
    private function updateGoodsRelations(Goods $goods, array $response): void
    {
        $catId = $response['cat_id'] ?? null;
        if (null !== $catId) {
            $category = $this->categoryRepository->find($catId);
            $goods->setCategory($category instanceof Category ? $category : null);
        }

        $countryId = $response['country_id'] ?? null;
        if (null !== $countryId) {
            $country = $this->countryRepository->find($countryId);
            $goods->setCountry($country instanceof Country ? $country : null);
        }

        $templateId = $response['cost_template_id'] ?? null;
        if (null !== $templateId) {
            $template = $this->templateRepository->find($templateId);
            $goods->setCostTemplate($template instanceof LogisticsTemplate ? $template : null);
        }
    }

    /**
     * @param array<mixed> $skuList
     */
    private function updateSkuListFromResponse(Goods $goods, array $skuList): void
    {
        foreach ($skuList as $item) {
            if (!is_array($item)) {
                continue;
            }
            $this->updateSkuFromResponse($goods, $item);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function updateSkuFromResponse(Goods $goods, array $item): void
    {
        $sku = $this->findOrCreateSku($goods, $item);
        $this->updateSkuFields($sku, $item);

        $this->entityManager->persist($sku);
        $this->entityManager->flush();
    }

    /**
     * @param array<mixed> $item
     */
    private function findOrCreateSku(Goods $goods, array $item): Sku
    {
        $skuId = $item['sku_id'] ?? null;

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
     * @param array<mixed> $item
     */
    private function updateSkuFields(Sku $sku, array $item): void
    {
        $this->skuDataMapper->mapFields($sku, $item);
    }
}
