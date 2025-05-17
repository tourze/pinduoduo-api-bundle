<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\MallCharacter;
use PinduoduoApiBundle\Enum\MerchantType;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '店铺信息')]
#[ORM\Entity(repositoryClass: MallRepository::class)]
#[ORM\Table(name: 'ims_pdd_mall', options: ['comment' => '店铺信息'])]
class Mall implements ApiArrayInterface
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    #[ORM\Column(length: 120, options: ['comment' => '店铺名称'])]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '店铺描述'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '店铺logo'])]
    private ?string $logo = null;

    #[ORM\Column(nullable: true, enumType: MerchantType::class, options: ['comment' => '店铺类型'])]
    private ?MerchantType $merchantType = null;

    #[ORM\Column(nullable: true, enumType: MallCharacter::class, options: ['comment' => '店铺身份'])]
    private ?MallCharacter $mallCharacter = null;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'mall', targetEntity: AuthLog::class)]
    private Collection $authLogs;

    #[ORM\Column(nullable: true, options: ['comment' => '是否签署多多进宝协议'])]
    private ?bool $cpsProtocolStatus = null;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'mall', targetEntity: LogisticsTemplate::class, orphanRemoval: true)]
    private Collection $logisticsTemplates;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'mall', targetEntity: Video::class, orphanRemoval: true)]
    private Collection $videos;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'mall', targetEntity: AuthCat::class, orphanRemoval: true)]
    private Collection $authCats;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function __construct()
    {
        $this->authLogs = new ArrayCollection();
        $this->logisticsTemplates = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->authCats = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getMerchantType(): ?MerchantType
    {
        return $this->merchantType;
    }

    public function setMerchantType(?MerchantType $merchantType): static
    {
        $this->merchantType = $merchantType;

        return $this;
    }

    public function getMallCharacter(): ?MallCharacter
    {
        return $this->mallCharacter;
    }

    public function setMallCharacter(?MallCharacter $mallCharacter): static
    {
        $this->mallCharacter = $mallCharacter;

        return $this;
    }

    /**
     * @return Collection<int, AuthLog>
     */
    public function getAuthLogs(): Collection
    {
        return $this->authLogs;
    }

    public function addAuthLog(AuthLog $authLog): static
    {
        if (!$this->authLogs->contains($authLog)) {
            $this->authLogs->add($authLog);
            $authLog->setMall($this);
        }

        return $this;
    }

    public function removeAuthLog(AuthLog $authLog): static
    {
        if ($this->authLogs->removeElement($authLog)) {
            // set the owning side to null (unless already changed)
            if ($authLog->getMall() === $this) {
                $authLog->setMall(null);
            }
        }

        return $this;
    }

    public function isCpsProtocolStatus(): ?bool
    {
        return $this->cpsProtocolStatus;
    }

    public function setCpsProtocolStatus(?bool $cpsProtocolStatus): static
    {
        $this->cpsProtocolStatus = $cpsProtocolStatus;

        return $this;
    }

    /**
     * @return Collection<int, LogisticsTemplate>
     */
    public function getLogisticsTemplates(): Collection
    {
        return $this->logisticsTemplates;
    }

    public function addLogisticsTemplate(LogisticsTemplate $logisticsTemplate): static
    {
        if (!$this->logisticsTemplates->contains($logisticsTemplate)) {
            $this->logisticsTemplates->add($logisticsTemplate);
            $logisticsTemplate->setMall($this);
        }

        return $this;
    }

    public function removeLogisticsTemplate(LogisticsTemplate $logisticsTemplate): static
    {
        if ($this->logisticsTemplates->removeElement($logisticsTemplate)) {
            // set the owning side to null (unless already changed)
            if ($logisticsTemplate->getMall() === $this) {
                $logisticsTemplate->setMall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setMall($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getMall() === $this) {
                $video->setMall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AuthCat>
     */
    public function getAuthCats(): Collection
    {
        return $this->authCats;
    }

    public function addAuthCat(AuthCat $authCat): static
    {
        if (!$this->authCats->contains($authCat)) {
            $this->authCats->add($authCat);
            $authCat->setMall($this);
        }

        return $this;
    }

    public function removeAuthCat(AuthCat $authCat): static
    {
        if ($this->authCats->removeElement($authCat)) {
            // set the owning side to null (unless already changed)
            if ($authCat->getMall() === $this) {
                $authCat->setMall(null);
            }
        }

        return $this;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'logo' => $this->getLogo(),
        ];
    }
}
