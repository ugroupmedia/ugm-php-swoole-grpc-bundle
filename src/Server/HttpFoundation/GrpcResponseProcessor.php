<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Server\HttpFoundation;

use ErrorException;
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

            // TODO: we should probably extend Symfony's Response class
            //       and proper trailers to it instead of messing with headers
            if ($httpFoundationResponse->headers->has('Grpc-Message')) {
                $value = $httpFoundationResponse->headers->get('Grpc-Message', '');
                $httpFoundationResponse->headers->remove('Grpc-Message');
                $swooleResponse->trailer('Grpc-Message', $value);
            }

            try {
                if ($httpFoundationResponse->headers->has('Grpc-Status-Details-Bin')) {
                    $value = $httpFoundationResponse->headers->get('Grpc-Status-Details-Bin', '');
                    $httpFoundationResponse->headers->remove('Grpc-Status-Details-Bin');
                    $swooleResponse->trailer('Grpc-Status-Details-Bin', $value);
                }
            } catch (ErrorException $e) {
                // Setting too long value throws an exception.
                // It's not so critical, client simply won't get some error details.
            }

            if ($httpFoundationResponse->headers->has('Grpc-Status')) {
                $value = $httpFoundationResponse->headers->get('Grpc-Status', '');
                $httpFoundationResponse->headers->remove('Grpc-Status');
                $swooleResponse->trailer('Grpc-Status', $value);
            } else {
                $swooleResponse->trailer('Grpc-Status', '0');
            }
        }

        $this->decorated->process($httpFoundationResponse, $swooleResponse);
    }
}
