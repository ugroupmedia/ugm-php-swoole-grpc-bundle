<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Configurator;

use K911\Swoole\Server\Configurator\ConfiguratorInterface;
use Swoole\Http\Server;

final class WithHttp2EnabledConfiguration implements ConfiguratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(Server $server): void
    {
        $server->set([
            'open_http2_protocol' => true
        ]);
    }
}
