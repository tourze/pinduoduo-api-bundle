<?php

namespace PinduoduoApiBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class PinduoduoApiExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
