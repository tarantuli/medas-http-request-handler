<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};
use Medas\HttpRequestHandler\{
    Attributes\RequestDataObject,
    Exceptions\ParameterTypeIsNotAClass,
    RequestDataManager
};

#[Service]
readonly class RequestDataObjectResolver implements ParameterResolver
{
    public function __construct(
        private RequestDataManager $requestDataManager,
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

        $className = normalizeType($parameter->getType())[0];

        if (!class_exists($className)) {
            throw new ParameterTypeIsNotAClass($parameter, $className);
        }

        $object = new ($className);

        if ($argument->fromBody) {
            $bodyData = $this->requestDataManager->get()->bodyData->data();

            $this->setData($object, $bodyData);
        }

        if ($argument->fromQuery) {
            $queryData = $this->requestDataManager->get()->uri->query;

            $this->setData($object, $queryData);
        }

        return new ParameterResolverResult(true, $object);
    }

    private function setData(object $object, array $data): void
    {
        foreach ($data as $name => $value) {
            if (property_exists($object, $name)) {
                $object->$name = $value;
            }
        }
    }
}
