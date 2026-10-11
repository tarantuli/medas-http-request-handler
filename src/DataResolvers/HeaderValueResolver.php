<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{Attributes\HeaderValue, Request\HeaderFinder, RequestFactory};

readonly class HeaderValueResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -55;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): HeaderValue|null
    {
        return attribute(HeaderValue::class, $parameter);
    }

    /**
     * @param HeaderValue $compiled
     */
    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $serverData = service(RequestFactory::class)->get()->serverData;
        $value = service(HeaderFinder::class)->find($serverData, $compiled->name);

        return new ParameterResolverResult($value !== null, $value);
    }
}
