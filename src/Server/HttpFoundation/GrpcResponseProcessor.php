<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Server\HttpFoundation;

use K911\Swoole\Bridge\Symfony\HttpFoundation\ResponseProcessorInterface;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function pack;
use function strlen;

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
        $contentType = $httpFoundationResponse->headers->get('Content-Type');
        if ($contentType === 'application/grpc') {
            $swooleResponse->trailer('grpc-status', $httpFoundationResponse->getStatusCode());
            $httpFoundationResponse->setStatusCode(200);
        }

        $this->decorated->process($httpFoundationResponse, $swooleResponse);
    }
}
