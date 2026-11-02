<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{Attributes\BodyArgument, RequestFactory};

/**
 * Injects #[BodyArgument('path.to.value')]: a value from the request body, by dot-notation path.
 * When the parameter's type is a class and the value an array, it's cast into an object of that
 * class (see RequestValueCaster).
 */
readonly class BodyDataResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -50;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): CompiledRequestArgument|null
    {
        if (!$attribute = attribute(BodyArgument::class, $parameter)) {
            return null;
        }

        return CompiledRequestArgument::fromParameter($attribute->name, $parameter);
    }

    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        /** @var CompiledRequestArgument $compiled */
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $bodyData = service(RequestFactory::class)->get()->bodyData->data();
        $value = $bodyData;

        foreach (explode('.', $compiled->name) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return new ParameterResolverResult(false);
            }

            $value = $value[$part];
        }

        // A JSON body has its own types, so its strings aren't coerced
        return new ParameterResolverResult(
            true,
            service(RequestValueCaster::class)->resolve($value, $compiled, coerceStrings: false)
        );
    }
}
