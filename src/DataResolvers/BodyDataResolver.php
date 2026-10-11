<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{Attributes\BodyArgument, RequestFactory};

readonly class BodyDataResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -50;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): BodyArgument|null
    {
        return attribute(BodyArgument::class, $parameter);
    }

    /**
     * @param BodyArgument $compiled
     */
    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $bodyData = service(RequestFactory::class)->get()->bodyData->data();
        $parts = explode('.', $compiled->name);
        $array = &$bodyData;

        foreach ($parts as $part) {
            if (!array_key_exists($part, $array)) {
                return new ParameterResolverResult(false);
            }

            $array = &$array[$part];
        }

        return new ParameterResolverResult(true, $array);
    }
}
