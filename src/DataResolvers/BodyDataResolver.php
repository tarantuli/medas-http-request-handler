<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\BodyArgument, RequestFactory};

readonly class BodyDataResolver implements ParameterResolver
{
    public function __construct(
        private RequestFactory $requestFactory,
    )
    {
    }

    public function priority(): int
    {
        return -50;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(BodyArgument::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $bodyData = $this->requestFactory->get()->bodyData->data();
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
