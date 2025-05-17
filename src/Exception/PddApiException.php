<?php

namespace PinduoduoApiBundle\Exception;

class PddApiException extends \Exception
{
    public function __construct(array $errorResponse)
    {
        $message = $errorResponse['error_msg'];
        $code = $errorResponse['error_code'];
        $this->setSubMsg($errorResponse['sub_msg'] ?? null);
        parent::__construct($message, $code);
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
