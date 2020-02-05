<?php

declare(strict_types=1);

namespace Ugm\SwooleGrpc\ArgumentResolver;

use Spiral\GRPC\ContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Ugm\SwooleGrpc\Context\GrpcContextManager;

class GrpcContextResolver implements ArgumentValueResolverInterface
{
    /**
     * @var GrpcContextManager
     */
    private $contextManager;

    public function __construct(GrpcContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), ContextInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield $this->contextManager->getContext();
    }
}
