<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Service;

use DateTimeImmutable;
use PinduoduoApiBundle\Entity\Goods\Sku;

/**
 * SKU数据映射器，负责将API响应数据映射到Sku实体
 */
class SkuDataMapper
{
    /**
     * @param array<mixed> $item
     */
    public function mapFields(Sku $sku, array $item): void
    {
        $this->mapStringFields($sku, $item);
        $this->mapIntegerFields($sku, $item);
        $this->mapBooleanFields($sku, $item);
        $this->mapArrayFields($sku, $item);
        $this->mapDateFields($sku, $item);
    }

    /**
     * @param array<mixed> $item
     */
    private function mapStringFields(Sku $sku, array $item): void
    {
        $sku->setOutSkuSn(is_string($item['out_sku_sn'] ?? null) ? $item['out_sku_sn'] : null);
        $sku->setThumbUrl(is_string($item['thumb_url'] ?? null) ? $item['thumb_url'] : null);
        $sku->setOutSourceSkuId(is_string($item['out_source_sku_id'] ?? null) ? $item['out_source_sku_id'] : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function mapIntegerFields(Sku $sku, array $item): void
    {
        $sku->setMultiPrice(is_int($item['multi_price'] ?? null) ? $item['multi_price'] : null);
        $sku->setQuantity(is_int($item['quantity'] ?? null) ? $item['quantity'] : null);
        $sku->setReserveQuantity(is_int($item['reserve_quantity'] ?? null) ? $item['reserve_quantity'] : null);
        $sku->setLength(is_int($item['length'] ?? null) ? $item['length'] : null);
        $sku->setWeight(is_int($item['weight'] ?? null) ? $item['weight'] : null);
        $sku->setPrice(is_int($item['price'] ?? null) ? $item['price'] : null);
        $sku->setLimitQuantity(is_int($item['limit_quantity'] ?? null) ? $item['limit_quantity'] : null);
    }

    /**
     * @param array<mixed> $item
     */
    private function mapBooleanFields(Sku $sku, array $item): void
    {
        $sku->setOnsale((bool) ($item['is_onsale'] ?? false));
    }

    /**
     * @param array<mixed> $item
     */
    private function mapArrayFields(Sku $sku, array $item): void
    {
        $overseaSku = $item['oversea_sku'] ?? null;
        if (is_array($overseaSku)) {
            /** @var array<string, mixed> $validOverseaSku */
            $validOverseaSku = $overseaSku;
            $sku->setOverseaSku($validOverseaSku);
        } else {
            $sku->setOverseaSku(null);
        }

        $spec = $item['spec'] ?? null;
        if (is_array($spec)) {
            /** @var array<string, mixed> $validSpec */
            $validSpec = $spec;
            $sku->setSpecDetails($validSpec);
        } else {
            $sku->setSpecDetails(null);
        }

        $skuPropertyList = $item['sku_property_list'] ?? null;
        if (is_array($skuPropertyList)) {
            /** @var array<string, mixed> $validSkuPropertyList */
            $validSkuPropertyList = $skuPropertyList;
            $sku->setSkuProperties($validSkuPropertyList);
        } else {
            $sku->setSkuProperties(null);
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function mapDateFields(Sku $sku, array $item): void
    {
        $preSaleTimeValue = $item['sku_pre_sale_time'] ?? null;
        if (is_int($preSaleTimeValue) && 0 !== $preSaleTimeValue) {
            $preSaleTime = \DateTimeImmutable::createFromFormat('U', (string) $preSaleTimeValue);
            $sku->setPreSaleTime(false !== $preSaleTime ? $preSaleTime : null);
        } else {
            $sku->setPreSaleTime(null);
        }
    }
}
