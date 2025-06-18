<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LogisticsTemplateRepository::class)]
#[ORM\Table(name: 'ims_pdd_logistics_template', options: ['comment' => '商品运费模版'])]
class LogisticsTemplate implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    #[ORM\ManyToOne(inversedBy: 'logisticsTemplates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[ORM\Column(enumType: CostType::class, options: ['comment' => '计费方式'])]
    private ?CostType $costType = null;

    #[ORM\Column(length: 100, options: ['comment' => '运费模板名称'])]
    private ?string $name = null;

    use TimestampableAware;

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): static
    {
        $this->mall = $mall;

        return $this;
    }

    public function getCostType(): ?CostType
    {
        return $this->costType;
    }

    public function setCostType(CostType $costType): static
    {
        $this->costType = $costType;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
