<?php

namespace PinduoduoApiBundle\Request;

use HttpClientBundle\Request\RequestInterface;
use PinduoduoApiBundle\Entity\Account;

/**
 * 拼多多API基础请求类
 */
class BasePddRequest implements RequestInterface
{
    private Account $account;

    private string $type;

    /**
     * @var array<string, mixed>
     */
    private array $params = [];

    private ?string $accessToken = null;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRequestPath(): string
    {
        return '/api/router';
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => $this->params,
        ];
    }
}
