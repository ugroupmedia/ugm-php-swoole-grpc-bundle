<?php

declare(strict_types=1);

namespace Ugm\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ugm\SwooleGrpcBundle\EventListener\RouterListener;

class GrpcServiceMapPass implements CompilerPassInterface
{
    private $grpcServiceTag;

    public function __construct(string $grpcServiceTag = 'grpc.service')
    {
        $this->grpcServiceTag = $grpcServiceTag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $map = [];
        foreach ($container->findTaggedServiceIds($this->grpcServiceTag) as $id => $attr) {
            $service = $container->getDefinition($id);
            $class = $service->getClass();
            $map[$class::NAME] = $class;
        }

        $container
            ->getDefinition(RouterListener::class)
            ->setArgument(0, $map);
    }
}
