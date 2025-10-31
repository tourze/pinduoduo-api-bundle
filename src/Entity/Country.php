<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\CountryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'ims_pdd_country', options: ['comment' => '商品地区/国家'])]
class Country implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '国家或地区名称'])]
    private ?string $name = null;

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
