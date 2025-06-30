<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\QueryArgument, Exceptions, RequestDataManager};

#[Service]
readonly class QueryDataResolver implements ParameterResolver
{
    public function __construct(
        private RequestDataManager $requestDataManager,
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

        $queryData = $this->requestDataManager->get()->uri->query;

        if (!isset($queryData[$argument->name])) {
            throw new Exceptions\QueryArgumentIsMissing($argument->name);
        }

        return new ParameterResolverResult(true, $queryData[$argument->name]);
    }
}
