<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\UploadImgRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.img.upload
 */
#[ORM\Entity(repositoryClass: UploadImgRepository::class)]
#[ORM\Table(name: 'ims_pdd_upload_img', options: ['comment' => '商品图片'])]
class UploadImg implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'videos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mall $mall = null;

    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, options: ['comment' => '商品原始图片'])]
    private ?string $file = null;

    #[Assert\Length(max: 150)]
    #[Assert\Url]
    #[ORM\Column(length: 150, nullable: true, options: ['comment' => '图片URL'])]
    private ?string $url = null;

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
