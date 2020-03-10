<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\DependencyInjection\CompilerPass;

use Spiral\GRPC\MethodInterface;
use Spiral\GRPC\ServiceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ugm\SwooleGrpc\DependencyInjection\Exception\ServiceConfigurationException;
use Ugm\SwooleGrpc\EventListener\RouterListener;

class GrpcServiceMapPass implements CompilerPassInterface
{
    private $grpcServiceTag;
    private $grpcMethodTag;

    public function __construct(string $grpcServiceTag = 'grpc.service', string $grpcMethodTag = 'grpc.method')
    {
        $this->grpcServiceTag = $grpcServiceTag;
        $this->grpcMethodTag = $grpcMethodTag;
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

            if (!is_a($class, ServiceInterface::class, true)) {
                throw new ServiceConfigurationException($class, 'is tagged as "grpc.service",'
                    .' but it doesn\'t implement any ServiceInterface.'
                    .' All GRPC services should implement one service interface.');
            }

            $serviceName = $class::NAME;
            $serviceInterfacesFound = false;
            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getInterfaces() as $interface) {
                if ($interface->implementsInterface(ServiceInterface::class)) {
                    if ($serviceInterfacesFound === false) {
                        $serviceInterfacesFound = true;
                    } else {
                        throw new ServiceConfigurationException($class, 'implements more than one ServiceInterface. '
                            .'GRPC services should implement a single interface at a time.');
                    }
                    foreach ($interface->getMethods() as $method) {
                        $methodName = $method->getName();
                        $map["/{$serviceName}/{$methodName}"] = "{$serviceName}::{$methodName}";
                    }
                }
            }
        }


        if (interface_exists('Spiral\GRPC\MethodInterface')) {
            foreach ($container->findTaggedServiceIds($this->grpcMethodTag) as $id => $attr) {
                $service = $container->getDefinition($id);
                $class = $service->getClass();
                if (!is_a($class, MethodInterface::class, true)) {
                    throw new ServiceConfigurationException($class, 'is tagged as "grpc.method",'
                        .' but it doesn\'t implement MethodInterface.'
                        .' All GRPC method services should implement one method interface.');
                }

                $map['/'.$class::NAME] = "{$class}::__invoke";
            }
        }

        $container
            ->getDefinition(RouterListener::class)
            ->setArgument(0, $map);
    }
}
