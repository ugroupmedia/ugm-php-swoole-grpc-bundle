<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\DependencyInjection\Exception;

use Exception;

class ServiceConfigurationException extends Exception
{
    public function __construct(string $class, string $reason)
    {
        parent::__construct('Service "'.$class.'" '.$reason);
    }
}