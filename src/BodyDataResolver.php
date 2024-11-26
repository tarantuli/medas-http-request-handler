<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};

#[Service]
readonly class BodyDataResolver implements ParameterResolver
{
    public function __construct(
        private RequestDataManager $requestDataManager,
    )
    {
    }

    public function priority(): int
    {
        return -50;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(Attributes\BodyArgument::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $bodyData = $this->requestDataManager->get()->bodyData->data();
        $parts = explode('.', $argument->name);
        $array = &$bodyData;

        foreach ($parts as $part) {
            if (!isset($array[$part])) {
                throw new Exceptions\BodyArgumentIsMissing($argument->name);
            }

            $array = &$array[$part];
        }

        return new ParameterResolverResult(true, $array);
    }
}
