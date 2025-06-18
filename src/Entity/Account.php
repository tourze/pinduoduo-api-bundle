<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Repository\AccountRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'ims_pdd_account', options: ['comment' => '开放平台'])]
class Account implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    #[ORM\Column(length: 100, options: ['comment' => '应用名称'])]
    private string $title;

    #[ORM\Column(length: 120, options: ['comment' => 'ClientID'])]
    private string $clientId;

    #[ORM\Column(length: 120, options: ['comment' => 'ClientSecret'])]
    private string $clientSecret;

    #[ORM\Column(length: 60, nullable: true, enumType: ApplicationType::class, options: ['comment' => '应用类型'])]
    private ?ApplicationType $applicationType = null;

    #[Ignore]
    #[ORM\OneToMany(targetEntity: AuthLog::class, mappedBy: 'account')]
    private Collection $authLogs;

    public function __construct()
    {
        $this->authLogs = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getApplicationType(): ?ApplicationType
    {
        return $this->applicationType;
    }

    public function setApplicationType(?ApplicationType $applicationType): static
    {
        $this->applicationType = $applicationType;

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
            $authLog->setAccount($this);
        }

        return $this;
    }

    public function removeAuthLog(AuthLog $authLog): static
    {
        if ($this->authLogs->removeElement($authLog)) {
            // set the owning side to null (unless already changed)
            if ($authLog->getAccount() === $this) {
                $authLog->setAccount(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
