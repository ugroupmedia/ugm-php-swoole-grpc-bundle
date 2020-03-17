<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FormatListener implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $format = $request->getPreferredFormat(null);
        if (null !== $format) {
            return;
        }

        $request->setRequestFormat($request->getContentType());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 90],
        ];
    }
}
