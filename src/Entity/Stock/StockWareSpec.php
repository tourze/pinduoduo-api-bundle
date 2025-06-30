<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSpecRepository;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: StockWareSpecRepository::class)]
#[ORM\Table(name: 'pdd_stock_ware_spec', options: ['comment' => '拼多多货品规格信息'])]
class StockWareSpec implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: StockWareSku::class, inversedBy: 'specs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?StockWareSku $stockWareSku = null;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '规格ID'])]
    private string $specId;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '规格名称'])]
    private string $specKey;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '规格值'])]
    private string $specValue;

    public function getStockWareSku(): ?StockWareSku
    {
        return $this->stockWareSku;
    }

    public function setStockWareSku(?StockWareSku $stockWareSku): self
    {
        $this->stockWareSku = $stockWareSku;
        return $this;
    }

    public function getSpecId(): string
    {
        return $this->specId;
    }

    public function setSpecId(string $specId): self
    {
        $this->specId = $specId;
        return $this;
    }

    public function getSpecKey(): string
    {
        return $this->specKey;
    }

    public function setSpecKey(string $specKey): self
    {
        $this->specKey = $specKey;
        return $this;
    }

    public function getSpecValue(): string
    {
        return $this->specValue;
    }

    public function setSpecValue(string $specValue): self
    {
        $this->specValue = $specValue;
        return $this;
    }


    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
} 