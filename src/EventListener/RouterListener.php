<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\EventListener;

use Spiral\GRPC\Exception\UnimplementedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function strpos;

class RouterListener implements EventSubscriberInterface
{
    /**
     * @var array<string, string>
     */
    private $serviceMap;

    /**
     * @param array<string, string> $serviceMap
     */
    public function __construct(array $serviceMap)
    {
        $this->serviceMap = $serviceMap;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        if ($request->getMethod() === Request::METHOD_POST
            && strpos($request->headers->get('CONTENT_TYPE'), 'application/grpc') === 0
        ) {
            $uri = $request->getRequestUri();
            if (isset($this->serviceMap[$uri])) {
                $request->attributes->add(['_controller' => $this->serviceMap[$uri]]);
            } else {
                throw new UnimplementedException("Method {$uri} is not implemented.");
            }
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 64]],
        ];
    }
}
