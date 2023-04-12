<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Attributes\BodyArgument;
use Medas\HttpRequestHandler\Exceptions\BodyArgumentIsMissing;
use Medas\HttpRequestHandler\Request\RequestDataManager;
use Medas\ServiceManager\ParameterResolving\ParameterResolver;
use Medas\ServiceManager\Service;

#[Service]
class BodyDataResolver implements ParameterResolver
{
    private mixed $result;

    public function __construct(
        private readonly RequestDataManager $requestDataManager,
    )
    {
    }

    public function priority(): int
    {
        return -50;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): bool
    {
        if (!$argument = attribute(BodyArgument::class, $parameter)) {
            return false;
        }

        $bodyData = $this->requestDataManager->get()->bodyData;

        if (!isset($bodyData[$argument->name])) {
            throw new BodyArgumentIsMissing($argument->name);
        }

        $this->result = $bodyData[$argument->name];

        return true;
    }

    public function result(): mixed
    {
        $returnValue = $this->result;
        $this->result = null;

        return $returnValue;
    }
}
