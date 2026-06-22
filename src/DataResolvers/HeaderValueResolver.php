<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\HeaderValue, Request\HeaderFinder, RequestFactory};

readonly class HeaderValueResolver implements ParameterResolver
{
    public function priority(): int
    {
        return -55;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(HeaderValue::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $serverData = service(RequestFactory::class)->get()->serverData;
        $value = service(HeaderFinder::class)->find($serverData, $argument->name);

        return new ParameterResolverResult($value !== null, $value);
    }
}
