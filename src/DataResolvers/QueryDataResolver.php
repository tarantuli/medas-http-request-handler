<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{Attributes\QueryArgument, Exceptions, RequestFactory};

/**
 * Injects #[QueryArgument('name')]: a query string parameter. Query values are always strings, so
 * they're converted to an int, float or bool parameter's type; an array (?search[tags][]=3) is cast
 * into an object when the parameter's type is a class (see RequestValueCaster).
 */
readonly class QueryDataResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -40;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): CompiledRequestArgument|null
    {
        if (!$attribute = attribute(QueryArgument::class, $parameter)) {
            return null;
        }

        return CompiledRequestArgument::fromParameter($attribute->name, $parameter);
    }

    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        /** @var CompiledRequestArgument $compiled */
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $queryData = service(RequestFactory::class)->get()->uri->query;

        if (!array_key_exists($compiled->name, $queryData)) {
            throw new Exceptions\QueryArgumentIsMissing($compiled->name);
        }

        return new ParameterResolverResult(
            true,
            service(RequestValueCaster::class)->resolve(
                $queryData[$compiled->name],
                $compiled,
                coerceStrings: true
            )
        );
    }
}
