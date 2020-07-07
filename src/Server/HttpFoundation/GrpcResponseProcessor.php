<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Server\HttpFoundation;

use K911\Swoole\Bridge\Symfony\HttpFoundation\ResponseProcessorInterface;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use function pack;
use function strlen;
use function strpos;

final class GrpcResponseProcessor implements ResponseProcessorInterface
{
    private $decorated;

    public function __construct(ResponseProcessorInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function process(HttpFoundationResponse $httpFoundationResponse, SwooleResponse $swooleResponse): void
    {
        $contentType = (string)$httpFoundationResponse->headers->get('Content-Type');
        if (strpos($contentType, 'application/grpc') === 0) {
            $httpFoundationResponse->setStatusCode(200);

            $content = $httpFoundationResponse->getContent();
            $content = pack('CN', 0, strlen($content)).$content;
            $httpFoundationResponse->setContent($content);

            if ($httpFoundationResponse->headers->has('Grpc-Message')) {
                $value = $httpFoundationResponse->headers->get('Grpc-Message', '');
                $httpFoundationResponse->headers->remove('Grpc-Message');
                $httpFoundationResponse->headers->set('Pnp-Grpc-Message', $value);
            }

            if ($httpFoundationResponse->headers->has('Grpc-Status-Details-Bin')) {
                $value = $httpFoundationResponse->headers->get('Grpc-Status-Details-Bin', '');
                $httpFoundationResponse->headers->remove('Grpc-Status-Details-Bin');
                $httpFoundationResponse->headers->set('Pnp-Grpc-Status-Details-Bin', $value);
            }

            if ($httpFoundationResponse->headers->has('Grpc-Status')) {
                $value = $httpFoundationResponse->headers->get('Grpc-Status', '');
                $httpFoundationResponse->headers->remove('Grpc-Status');
                $httpFoundationResponse->headers->set('Pnp-Grpc-Status', $value);
            } else {
                $httpFoundationResponse->headers->set('Pnp-Grpc-Status', '0');
            }
        }

        $this->decorated->process($httpFoundationResponse, $swooleResponse);
    }
}
