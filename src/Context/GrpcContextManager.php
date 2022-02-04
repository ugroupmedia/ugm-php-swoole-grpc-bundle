<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Context;

use Spiral\RoadRunner\GRPC\Context;
use Spiral\RoadRunner\GRPC\ResponseHeaders;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GrpcContextManager implements EventSubscriberInterface
{
    /**
     * @var Context
     */
    private $context;

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMasterRequest()) {

            $context = new Context([
                'request' => $event->getRequest(),
                ResponseHeaders::class => new ResponseHeaders(),
            ]);
            $this->context = $context;
        }
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if ($event->isMasterRequest()) {
            $this->context = null;
        }
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1024]],
            KernelEvents::TERMINATE => [['onKernelTerminate', 1024]],
        ];
    }
}
