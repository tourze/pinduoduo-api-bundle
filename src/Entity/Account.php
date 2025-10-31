<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Enum\ApplicationType;
use PinduoduoApiBundle\Repository\AccountRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'ims_pdd_account', options: ['comment' => '开放平台'])]
class Account implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '应用名称'])]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, options: ['comment' => 'ClientID'])]
    private string $clientId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, options: ['comment' => 'ClientSecret'])]
    private string $clientSecret;

    #[Assert\Choice(callback: [ApplicationType::class, 'cases'])]
    #[ORM\Column(length: 60, nullable: true, enumType: ApplicationType::class, options: ['comment' => '应用类型'])]
    private ?ApplicationType $applicationType = null;

    /**
     * @var Collection<int, AuthLog>
     */
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

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getApplicationType(): ?ApplicationType
    {
        return $this->applicationType;
    }

    public function setApplicationType(?ApplicationType $applicationType): void
    {
        $this->applicationType = $applicationType;
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
        return $this->getId() ?? '';
    }
}
