<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Ugm\SwooleGrpc\DependencyInjection\CompilerPass\GrpcServiceMapPass;

final class SwooleGrpcBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GrpcServiceMapPass());
    }
}
