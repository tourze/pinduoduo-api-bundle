<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\VideoRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.gooods.sku.measurement.list
 */
#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: 'ims_pdd_video', options: ['comment' => '商品视频'])]
class Video implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'videos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, options: ['comment' => '视频URL'])]
    private ?string $url = null;

    #[Assert\Type(type: 'int')]
    #[ORM\Column(nullable: true, options: ['comment' => '状态'])]
    private ?int $status = null;

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
