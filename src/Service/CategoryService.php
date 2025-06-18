<?php

namespace PinduoduoApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PinduoduoApiBundle\Entity\Goods\Category;
use PinduoduoApiBundle\Entity\Goods\Spec;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Repository\Goods\SpecRepository;

class CategoryService
{
    public function __construct(
        private readonly SdkService $sdkService,
        private readonly EntityManagerInterface $entityManager,
        private readonly SpecRepository $specRepository,
    )
    {
    }

    public function syncSpecList(Mall $mall, Category $category): void
    {
        $response = $this->sdkService->request($mall, 'pdd.goods.spec.get', [
            'cat_id' => $category->getId(),
        ]);
        if (!isset($response['goods_spec_get_response'])) {
            return;
        }

        $specList = [];
        foreach ($response['goods_spec_get_response']['goods_spec_list'] as $item) {
            $spec = $this->specRepository->find($item['parent_spec_id']);
            if ($spec === null) {
                $spec = new Spec();
            }
            $spec->setName($item['parent_spec_name']);
            $this->entityManager->persist($spec);
            $specList[] = $spec;
        }
        $this->entityManager->flush();

        foreach ($category->getSpecs() as $spec) {
            $category->removeSpec($spec);
        }
        foreach ($specList as $spec) {
            $category->addSpec($spec);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}
