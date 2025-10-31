<?php

namespace PinduoduoApiBundle\Exception;

class MallNotFoundException extends \RuntimeException
{
    public function __construct(string $message = '找不到授权店铺', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
