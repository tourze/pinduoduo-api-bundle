<?php

namespace PinduoduoApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Spec;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\SpecRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class CategoryService
{
    public function __construct(
        private readonly PinduoduoClient $pinduoduoClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly SpecRepository $specRepository,
    ) {
    }

    public function syncSpecList(Mall $mall, Category $category): void
    {
        try {
            $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.spec.get', [
                'cat_id' => $category->getId(),
            ]);
        } catch (\Exception $e) {
            return;
        }

        if (!isset($response['goods_spec_list']) || !is_array($response['goods_spec_list'])) {
            return;
        }

        $specList = [];
        foreach ($response['goods_spec_list'] as $item) {
            if (!is_array($item) || !isset($item['parent_spec_id'], $item['parent_spec_name'])) {
                continue;
            }

            $parentSpecName = is_string($item['parent_spec_name']) ? $item['parent_spec_name'] : '';
            $spec = $this->specRepository->find($item['parent_spec_id']);
            if (!$spec instanceof Spec) {
                $spec = new Spec();
            }
            $spec->setName($parentSpecName);
            $this->entityManager->persist($spec);
            $specList[] = $spec;
        }
        $this->entityManager->flush();

        foreach ($category->getSpecs() as $spec) {
            $category->removeSpec($spec);
        }
        foreach ($specList as $spec) {
            // $spec已经确保是Spec类型，无需重复检查
            $category->addSpec($spec);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}
