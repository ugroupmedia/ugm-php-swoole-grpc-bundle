<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\Server\HttpFoundation;

use K911\Swoole\Bridge\Symfony\HttpFoundation\RequestFactoryInterface;
use Swoole\Http\Request as SwooleRequest;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Ugm\SwooleGrpc\Server\HttpFoundation\Request as GrpcRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function strlen;
use function strpos;
use function substr;
use function unpack;

final class FromGrpcRequestFactory implements RequestFactoryInterface
{
    private $decorated;

    public function __construct(RequestFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function make(SwooleRequest $request): HttpFoundationRequest
    {
        if (isset($request->header['content-type']) && strpos($request->header['content-type'], 'application/grpc') === 0) {
            $server = \array_change_key_case($request->server, CASE_UPPER);

            // Add formatted headers to server
            foreach ($request->header as $key => $value) {
                $server['HTTP_'.\mb_strtoupper(\str_replace('-', '_', $key))] = $value;
            }

            // Cut off length from the actual content, verify content length
            $content = $request->rawContent();
            $lenPrefix = substr($content, 1, 4);
            if ($lenPrefix === false || strlen($lenPrefix) !== 4) {
                throw new BadRequestHttpException('Invalid request body.');
            }
            $len = unpack('N', $lenPrefix)[1];
            $data = substr($content, 5);
            if (strlen($data) !== $len) {
                throw new BadRequestHttpException('Invalid request body length');
            }

            return new GrpcRequest(
                $request->get ?? [],
                $request->post ?? [],
                [],
                $request->cookie ?? [],
                $request->files ?? [],
                $server,
                $data
            );
        }

        return $this->decorated->make($request);
    }
}
