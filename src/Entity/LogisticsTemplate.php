<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Repository\LogisticsTemplateRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LogisticsTemplateRepository::class)]
#[ORM\Table(name: 'ims_pdd_logistics_template', options: ['comment' => '商品运费模版'])]
class LogisticsTemplate implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'logisticsTemplates', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[Assert\Choice(callback: [CostType::class, 'cases'])]
    #[ORM\Column(enumType: CostType::class, options: ['comment' => '计费方式'])]
    private ?CostType $costType = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '运费模板名称'])]
    private ?string $name = null;

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
    }

    public function getCostType(): ?CostType
    {
        return $this->costType;
    }

    public function setCostType(CostType $costType): void
    {
        $this->costType = $costType;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
