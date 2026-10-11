<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{Attributes\QueryArgument, Exceptions, RequestFactory};

readonly class QueryDataResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -40;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): QueryArgument|null
    {
        return attribute(QueryArgument::class, $parameter);
    }

    /**
     * @param QueryArgument $compiled
     */
    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $queryData = service(RequestFactory::class)->get()->uri->query;

        if (!array_key_exists($compiled->name, $queryData)) {
            throw new Exceptions\QueryArgumentIsMissing($compiled->name);
        }

        return new ParameterResolverResult(true, $queryData[$compiled->name]);
    }
}
