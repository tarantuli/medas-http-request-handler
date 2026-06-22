<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\QueryArgument, Exceptions, RequestFactory};

readonly class QueryDataResolver implements ParameterResolver
{
    public function __construct(
        private RequestFactory $requestFactory,
    )
    {
    }

    public function priority(): int
    {
        return -40;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(QueryArgument::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $queryData = $this->requestFactory->get()->uri->query;

        if (!array_key_exists($argument->name, $queryData)) {
            throw new Exceptions\QueryArgumentIsMissing($argument->name);
        }

        return new ParameterResolverResult(true, $queryData[$argument->name]);
    }
}
