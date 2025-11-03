<?php

namespace PinduoduoApiBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\HttpClientBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\TempFileBundle\TempFileBundle;

class PinduoduoApiBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
            TempFileBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
