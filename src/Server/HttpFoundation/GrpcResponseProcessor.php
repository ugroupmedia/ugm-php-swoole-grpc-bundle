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
        if (!$httpFoundationResponse instanceof StreamedResponse) {
            $swooleResponse->trailer('grpc-status', '0');
            $content = $httpFoundationResponse->getContent();
            $content = pack('CN', 0, strlen($content)).$content;
            $httpFoundationResponse->setContent($content);
        }

        $this->decorated->process($httpFoundationResponse, $swooleResponse);
    }
}
