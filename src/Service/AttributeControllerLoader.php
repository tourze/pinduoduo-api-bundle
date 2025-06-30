<?php

namespace PinduoduoApiBundle\Service;

use PinduoduoApiBundle\Controller\Auth\CallbackController;
use PinduoduoApiBundle\Controller\Auth\RedirectController;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;

#[AutoconfigureTag(name: 'controller.service_arguments')]
class AttributeControllerLoader
{
    public function __construct(
        private readonly AttributeClassLoader $controllerLoader,
    ) {
    }

    /**
     * 自动加载控制器
     */
    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(RedirectController::class));
        $collection->addCollection($this->controllerLoader->load(CallbackController::class));
        return $collection;
    }
}