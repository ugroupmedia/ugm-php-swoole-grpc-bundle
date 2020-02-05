<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpcBundle\ArgumentResolver;

use Google\Protobuf\Internal\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class GrpcRequestMessageResolver implements ArgumentValueResolverInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), Message::class);
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        /** @var Message $in */
        $in = new $class();
        $in->mergeFromString($request->getContent());

        yield $in;
    }
}
