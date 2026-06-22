<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{Attributes\HeaderValue, Request\HeaderFinder, RequestFactory};

#[Service]
readonly class HeaderValueResolver implements ParameterResolver
{
    public function __construct(
        private HeaderFinder   $headerFinder,
        private RequestFactory $requestFactory,
    )
    {
    }

    public function priority(): int
    {
        return -55;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(HeaderValue::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $serverData = $this->requestFactory->get()->serverData;
        $value = $this->headerFinder->find($serverData, $argument->name);

        return new ParameterResolverResult($value !== null, $value);
    }
}
