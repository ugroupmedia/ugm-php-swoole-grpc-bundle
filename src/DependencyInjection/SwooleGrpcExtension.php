<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\DependencyInjection;

use Exception;
use Spiral\RoadRunner\GRPC\MethodInterface;
use Spiral\RoadRunner\GRPC\ServiceInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SwooleGrpcExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container
            ->registerForAutoconfiguration(ServiceInterface::class)
            ->addTag('grpc.service');
        if (interface_exists('Spiral\RoadRunner\GRPC\MethodInterface')) {
            $container
                ->registerForAutoconfiguration(MethodInterface::class)
                ->addTag('grpc.method');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'swoole_grpc';
    }
}
