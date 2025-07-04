<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AuthLogRepository::class)]
#[ORM\Table(name: 'ims_pdd_auth_log', options: ['comment' => '授权记录'])]
#[ORM\UniqueConstraint(name: 'ims_pdd_auth_log_idx_uniq', columns: ['account_id', 'mall_id'])]
class AuthLog implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    #[ORM\ManyToOne(inversedBy: 'authLogs')]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'authLogs')]
    private ?Mall $mall = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'Access Token'])]
    private ?string $accessToken = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'Refresh Token'])]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Access Token 过期时间'])]
    private ?\DateTimeInterface $tokenExpireTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '授权scope'])]
    private ?array $scope = null;

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): static
    {
        $this->mall = $mall;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getTokenExpireTime(): ?\DateTimeInterface
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeInterface $tokenExpireTime): static
    {
        $this->tokenExpireTime = $tokenExpireTime;

        return $this;
    }

    public function getScope(): ?array
    {
        return $this->scope;
    }

    public function setScope(?array $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->getId() ?? '');
    }
}
