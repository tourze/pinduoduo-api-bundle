<?php

namespace PinduoduoApiBundle\Request;

use HttpClientBundle\Request\RequestInterface;

/**
 * 刷新AccessToken请求
 */
class RefreshTokenRequest implements RequestInterface
{
    private string $clientId;

    private string $clientSecret;

    private string $refreshToken;

    public function getClientId(): string
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

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
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
        $params = [
            'client_id' => $this->clientId,
            'refresh_token' => $this->refreshToken,
            'type' => 'pdd.pop.auth.token.refresh',
            'timestamp' => (string) time(),
            'data_type' => 'JSON',
            'version' => 'V1',
        ];

        // 生成签名
        $params['sign'] = $this->generateSign($params, $this->clientSecret);

        return [
            'body' => http_build_query($params),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    /**
     * 生成签名
     *
     * @param array<string, mixed> $params
     */
    private function generateSign(array $params, string $secret): string
    {
        ksort($params);
        $str = $secret;
        foreach ($params as $key => $value) {
            if (!is_array($value) && null !== $value && (is_scalar($value) || (is_object($value) && method_exists($value, '__toString')))) {
                $str .= $key . (string) $value;
            }
        }
        $str .= $secret;

        return strtoupper(md5($str));
    }
}
