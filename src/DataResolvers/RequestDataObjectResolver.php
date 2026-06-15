<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{
    Attributes\RequestDataObject,
    Exceptions\ParameterTypeIsNotAClass,
    RequestFactory
};

readonly class RequestDataObjectResolver implements ParameterResolver
{
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

        $requestFactory = service(RequestFactory::class);
        $dataSetter = service(DataSetter::class);
        $object = new ($className);

        if ($argument->fromBody) {
            $bodyData = $requestFactory->get()->bodyData->data();

            $dataSetter->set($object, $bodyData);
        }

        if ($argument->fromQuery) {
            $queryData = $requestFactory->get()->uri->query;

            $dataSetter->set($object, $queryData);
        }

        return new ParameterResolverResult(true, $object);
    }
}
