<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    Interfaces\ArrayToObjectCaster,
    Interfaces\ParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{
    Attributes\RequestDataObject,
    Exceptions\ParameterTypeIsNotAClass,
    RequestFactory
};

readonly class RequestDataObjectResolver implements ParameterResolver
{
    public function __construct(
        private RequestFactory      $requestFactory,
        private ArrayToObjectCaster $caster,
    )
    {
    }

    public function priority(): int
    {
        return -30;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$argument = attribute(RequestDataObject::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        $className = normalizeType($parameter->getType())[0]->getName();

        if (!class_exists($className)) {
            throw new ParameterTypeIsNotAClass($parameter, $className);
        }

        $requestData = $this->requestFactory->get();
        $values = [];

        if ($argument->fromBody) {
            $values = array_merge($values, $requestData->bodyData->data());
        }

        if ($argument->fromQuery) {
            $values = array_merge($values, $requestData->uri->query);
        }

        $object = $this->caster->cast($values, $className);

        return new ParameterResolverResult(true, $object);
    }
}
