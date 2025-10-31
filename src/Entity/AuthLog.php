<?php

namespace PinduoduoApiBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\AuthLogRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AuthLogRepository::class)]
#[ORM\Table(name: 'ims_pdd_auth_log', options: ['comment' => '授权记录'])]
#[ORM\UniqueConstraint(name: 'ims_pdd_auth_log_idx_uniq', columns: ['account_id', 'mall_id'])]
class AuthLog implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array<string, mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    #[ORM\ManyToOne(inversedBy: 'authLogs', cascade: ['persist'])]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'authLogs', cascade: ['persist'])]
    private ?Mall $mall = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'Access Token'])]
    private ?string $accessToken = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'Refresh Token'])]
    private ?string $refreshToken = null;

    #[Assert\Type(type: '\DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Access Token 过期时间'])]
    private ?\DateTimeInterface $tokenExpireTime = null;

    /**
     * @var array<string>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '授权scope'])]
    private ?array $scope = null;

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getMall(): ?Mall
    {
        return $this->mall;
    }

    public function setMall(?Mall $mall): void
    {
        $this->mall = $mall;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getTokenExpireTime(): ?\DateTimeInterface
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeInterface $tokenExpireTime): void
    {
        $this->tokenExpireTime = $tokenExpireTime;
    }

    /**
     * @return array<string>|null
     */
    public function getScope(): ?array
    {
        return $this->scope;
    }

    /**
     * @param array<string>|null $scope
     */
    public function setScope(?array $scope): void
    {
        $this->scope = $scope;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
