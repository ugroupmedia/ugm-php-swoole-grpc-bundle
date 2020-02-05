<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\EventListener;

use Google\Protobuf\Internal\Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProtobufResponseListener implements EventSubscriberInterface
{
    public function onKernelView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        if ($result instanceof Message) {
            $response = new Response($result->serializeToString());
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        // Must be executed before SensioFrameworkExtraBundle's listener and before FOSRest
        return array(
            KernelEvents::VIEW => array('onKernelView', 50),
        );
    }
}
