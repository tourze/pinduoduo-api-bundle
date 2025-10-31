<?php

namespace PinduoduoApiBundle\Entity\Stock;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Stock\StockWareSpecRepository;
use Symfony\Component\Validator\Constraints as Assert;
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

    #[ORM\ManyToOne(targetEntity: StockWareSku::class, inversedBy: 'specs', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?StockWareSku $stockWareSku = null;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '规格ID'])]
    #[Assert\NotBlank(message: '规格ID不能为空')]
    #[Assert\Length(max: 20, maxMessage: '规格ID不能超过{{ limit }}位')]
    private string $specId;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '规格名称'])]
    #[Assert\NotBlank(message: '规格名称不能为空')]
    #[Assert\Length(max: 50, maxMessage: '规格名称不能超过{{ limit }}个字符')]
    private string $specKey;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '规格值'])]
    #[Assert\NotBlank(message: '规格值不能为空')]
    #[Assert\Length(max: 100, maxMessage: '规格值不能超过{{ limit }}个字符')]
    private string $specValue;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '父级规格ID'])]
    #[Assert\Length(max: 20, maxMessage: '父级规格ID不能超过{{ limit }}位')]
    private ?string $parentId = null;

    public function getStockWareSku(): ?StockWareSku
    {
        return $this->stockWareSku;
    }

    public function setStockWareSku(?StockWareSku $stockWareSku): void
    {
        $this->stockWareSku = $stockWareSku;
    }

    public function getSpecId(): string
    {
        return $this->specId;
    }

    public function setSpecId(string $specId): void
    {
        $this->specId = $specId;
    }

    public function getSpecKey(): string
    {
        return $this->specKey;
    }

    public function setSpecKey(string $specKey): void
    {
        $this->specKey = $specKey;
    }

    public function getSpecValue(): string
    {
        return $this->specValue;
    }

    public function setSpecValue(string $specValue): void
    {
        $this->specValue = $specValue;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
