<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\EventListener;

use Spiral\GRPC\ResponseHeaders;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Ugm\SwooleGrpc\Context\GrpcContextManager;

class AddResponseHeadersListener implements EventSubscriberInterface
{
    /**
     * @var GrpcContextManager
     */
    private $contextManager;

    public function __construct(GrpcContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->isMasterRequest() && null !== $this->contextManager->getContext()) {
            $response = $event->getResponse();
            /** @var ResponseHeaders $headers */
            $headers = $this->contextManager->getContext()->getValue(ResponseHeaders::class);
            $response->headers->add($headers->getIterator());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelResponse', 1],
        ];
    }
}
