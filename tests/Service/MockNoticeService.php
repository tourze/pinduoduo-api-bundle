<?php

namespace PinduoduoApiBundle\Tests\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Response;

#[Autoconfigure(public: true)]
class MockNoticeService
{
    public function weuiSuccess(string $title, string $subTitle = '', bool $showOp = true): Response
    {
        return new Response("Success: {$title}");
    }

    public function weuiError(string $title, string $subTitle = '', bool $showOp = true): Response
    {
        return new Response("Error: {$title}");
    }
}
