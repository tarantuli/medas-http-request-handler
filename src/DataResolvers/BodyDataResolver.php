<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\BodyArgument, RequestFactory};

readonly class BodyDataResolver implements ParameterResolver
{
    public function priority(): int
    {
        return -50;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(BodyArgument::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $bodyData = service(RequestFactory::class)->get()->bodyData->data();
        $parts = explode('.', $argument->name);
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
