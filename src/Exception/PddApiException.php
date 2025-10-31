<?php

namespace PinduoduoApiBundle\Exception;

class PddApiException extends \Exception
{
    /**
     * @param array<mixed> $errorResponse
     */
    public function __construct(array $errorResponse)
    {
        $message = $errorResponse['error_msg'] ?? '';
        $code = $errorResponse['error_code'] ?? 0;
        $subMsg = $errorResponse['sub_msg'] ?? null;
        $this->setSubMsg(is_string($subMsg) ? $subMsg : null);
        parent::__construct(is_string($message) ? $message : '', is_int($code) ? $code : 0);
    }

    private ?string $subMsg = null;

    public function getSubMsg(): ?string
    {
        return $this->subMsg;
    }

    public function setSubMsg(?string $subMsg): void
    {
        $this->subMsg = $subMsg;
    }
}
