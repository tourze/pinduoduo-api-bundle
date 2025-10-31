<?php

declare(strict_types=1);

namespace PinduoduoApiBundle\Service;

use DateTimeImmutable;
use PinduoduoApiBundle\Entity\Goods\Goods;
use PinduoduoApiBundle\Enum\Goods\DeliveryType;
use PinduoduoApiBundle\Enum\Goods\GoodsType;

/**
 * 商品数据映射器，负责将API响应数据映射到Goods实体
 */
class GoodsDataMapper
{
    /**
     * @param array<mixed> $response
     */
    public function mapBasicFields(Goods $goods, array $response): void
    {
        $this->mapStringFields($goods, $response);
        $this->mapIntegerFields($goods, $response);
        $this->mapBooleanFields($goods, $response);
        $this->mapArrayFields($goods, $response);
        $this->mapEnumFields($goods, $response);
        $this->mapDateFields($goods, $response);
    }

    /**
     * @param array<mixed> $response
     */
    private function mapStringFields(Goods $goods, array $response): void
    {
        $this->setStringField($goods->setOuterGoodsId(...), $response['outer_goods_id'] ?? null);
        $this->setStringField($goods->setGoodsName(...), $response['goods_name'] ?? null);
        $this->setStringField($goods->setMaiJiaZiTi(...), $response['mai_jia_zi_ti'] ?? null);
        $this->setStringField($goods->setCustomerNum(...), $response['customer_num'] ?? null);
        $this->setStringField($goods->setImageUrl(...), $response['image_url'] ?? null);
        $this->setStringField($goods->setWarmTips(...), $response['warm_tips'] ?? null);
        $this->setStringField($goods->setGoodsDesc(...), $response['goods_desc'] ?? null);
        $this->setStringField($goods->setWarehouse(...), $response['warehouse'] ?? null);
        $this->setStringField($goods->setOutSourceGoodsId(...), $response['out_source_goods_id'] ?? null);
        $this->setStringField($goods->setThumbUrl(...), $response['thumb_url'] ?? null);
        $this->setStringField($goods->setTinyName(...), $response['tiny_name'] ?? null);
    }

    /**
     * @param callable(string|null): void $setter
     * @param mixed $value
     */
    private function setStringField(callable $setter, mixed $value): void
    {
        $setter(is_string($value) ? $value : null);
    }

    /**
     * @param array<mixed> $response
     */
    private function mapIntegerFields(Goods $goods, array $response): void
    {
        $goods->setTwoPiecesDiscount(is_int($response['two_pieces_discount'] ?? null) ? $response['two_pieces_discount'] : null);
        $goods->setShipmentLimitSecond(is_int($response['shipment_limit_second'] ?? null) ? $response['shipment_limit_second'] : null);
        $goods->setOverseaType(is_int($response['oversea_type'] ?? null) ? $response['oversea_type'] : null);
        $goods->setOutSourceType(is_int($response['out_source_type'] ?? null) ? $response['out_source_type'] : null);
        $goods->setMarketPrice(is_int($response['market_price'] ?? null) ? $response['market_price'] : null);
        $goods->setOrderLimit(is_int($response['order_limit'] ?? null) ? $response['order_limit'] : null);
        $goods->setBuyLimit(is_int($response['buy_limit'] ?? null) ? $response['buy_limit'] : null);
    }

    /**
     * @param array<mixed> $response
     */
    private function mapBooleanFields(Goods $goods, array $response): void
    {
        $goods->setFolt(is_bool($response['is_folt'] ?? null) ? $response['is_folt'] : null);
        $goods->setSecondHand(is_bool($response['second_hand'] ?? null) ? $response['second_hand'] : null);
        $goods->setDeliveryOneDay((bool) ($response['delivery_one_day'] ?? false));
        $goods->setQuanGuoLianBao((bool) ($response['quan_guo_lian_bao'] ?? false));
        $goods->setInvoiceStatus((bool) ($response['invoice_status'] ?? false));
        $goods->setGroupPreSale((bool) ($response['is_group_pre_sale'] ?? false));
        $goods->setSkuPreSale((bool) ($response['is_sku_pre_sale'] ?? false));
        $goods->setPreSale((bool) ($response['is_pre_sale'] ?? false));
        $goods->setRefundable((bool) ($response['is_refundable'] ?? false));
        $goods->setLackOfWeightClaim((bool) ($response['lack_of_weight_claim'] ?? false));
    }

    /**
     * @param array<mixed> $response
     */
    private function mapArrayFields(Goods $goods, array $response): void
    {
        $this->setArrayField($goods->setElecGoodsAttributes(...), $response['elec_goods_attributes'] ?? null);
        $this->setArrayField($goods->setGoodsProperties(...), $response['goods_property_list'] ?? null);
        $this->setArrayField($goods->setVideoGallery(...), $response['video_gallery'] ?? null);
        $this->setArrayField($goods->setCarouselGalleryList(...), $response['carousel_gallery_list'] ?? null);
        $this->setArrayField($goods->setGoodsTravelAttr(...), $response['goods_travel_attr'] ?? null);
        $this->setArrayField($goods->setDetailGalleryList(...), $response['detail_gallery_list'] ?? null);
        $this->setArrayField($goods->setGoodsTradeAttr(...), $response['goods_trade_attr'] ?? null);
        $this->setArrayField($goods->setOverseaGoods(...), $response['oversea_goods'] ?? null);
    }

    /**
     * @param callable $setter
     * @param mixed $value
     */
    private function setArrayField(callable $setter, mixed $value): void
    {
        if (is_array($value)) {
            /** @var array<string, mixed>|array<string, string> $value */
            $setter($value);
        } else {
            $setter(null);
        }
    }

    /**
     * @param array<mixed> $response
     */
    private function mapEnumFields(Goods $goods, array $response): void
    {
        $deliveryType = $response['delivery_type'] ?? null;
        $goods->setDeliveryType(
            (is_int($deliveryType) || is_string($deliveryType))
                ? DeliveryType::tryFrom($deliveryType)
                : null
        );

        $goodsType = $response['goods_type'] ?? null;
        $goods->setGoodsType(
            (is_int($goodsType) || is_string($goodsType))
                ? GoodsType::tryFrom($goodsType)
                : null
        );
    }

    /**
     * @param array<mixed> $response
     */
    private function mapDateFields(Goods $goods, array $response): void
    {
        // zhi_huan_bu_xiu字段特殊处理
        $zhiHuanBuXiu = $response['zhi_huan_bu_xiu'] ?? null;
        $goods->setZhiHuanBuXiu(
            is_int($zhiHuanBuXiu)
                ? $zhiHuanBuXiu
                : (is_string($zhiHuanBuXiu) ? (int) $zhiHuanBuXiu : null)
        );

        // bad_fruit_claim字段特殊处理
        $badFruitClaim = $response['bad_fruit_claim'] ?? 0;
        $goods->setBadFruitClaim(
            is_int($badFruitClaim)
                ? $badFruitClaim
                : (\is_numeric($badFruitClaim) ? (int) $badFruitClaim : 0)
        );

        // pre_sale_time字段特殊处理
        $preSaleTime = $response['pre_sale_time'] ?? 0;
        if (is_int($preSaleTime) && $preSaleTime > 0) {
            $timestamp = \DateTimeImmutable::createFromFormat('U', (string) $preSaleTime);
            if (false !== $timestamp) {
                $goods->setPreSaleTime($timestamp);
            }
        }
    }
}
