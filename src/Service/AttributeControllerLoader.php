<?php

namespace PinduoduoApiBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('controller.service_arguments')]
class AttributeControllerLoader
{
    /**
     * 自动加载控制器
     */
    public function autoload(): array
    {
        return [
            \PinduoduoApiBundle\Controller\Auth\RedirectController::class,
            \PinduoduoApiBundle\Controller\Auth\CallbackController::class,
        ];
    }
}