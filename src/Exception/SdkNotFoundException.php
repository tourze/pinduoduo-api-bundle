<?php

namespace PinduoduoApiBundle\Exception;

class SdkNotFoundException extends \RuntimeException
{
    public function __construct(string $message = '找不到SDK授权', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}