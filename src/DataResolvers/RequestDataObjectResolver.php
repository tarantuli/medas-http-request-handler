<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{
    Attributes\RequestDataObject,
    Exceptions\ParameterTypeIsNotAClass,
    RequestFactory
};

#[Service]
readonly class RequestDataObjectResolver implements ParameterResolver
{
    public function __construct(
        private DataSetter     $dataSetter,
        private RequestFactory $requestFactory,
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

        $object = new ($className);

        if ($argument->fromBody) {
            $bodyData = $this->requestFactory->get()->bodyData->data();

            $this->dataSetter->set($object, $bodyData);
        }

        if ($argument->fromQuery) {
            $queryData = $this->requestFactory->get()->uri->query;

            $this->dataSetter->set($object, $queryData);
        }

        return new ParameterResolverResult(true, $object);
    }
}
