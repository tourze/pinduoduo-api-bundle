<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\MeasurementRepository;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.gooods.sku.measurement.list
 */
#[ORM\Entity(repositoryClass: MeasurementRepository::class)]
#[ORM\Table(name: 'ims_pdd_measurement', options: ['comment' => 'sku计量单位'])]
class Measurement implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[ORM\Column(length: 30, unique: true, options: ['comment' => '编码'])]
    private string $code;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '说明'])]
    private ?string $description = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
