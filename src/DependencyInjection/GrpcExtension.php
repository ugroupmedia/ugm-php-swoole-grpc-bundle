<?php

declare(strict_types=1);

namespace Ugm\DependencyInjection;

use Exception;
use Spiral\GRPC\ServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class GrpcExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ServiceInterface::class)
            ->addTag('grpc.service');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'grpc';
    }
}
