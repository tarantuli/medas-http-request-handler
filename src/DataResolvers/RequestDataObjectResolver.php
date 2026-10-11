<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\ArrayToObjectCaster,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};
use Medas\HttpRequestHandler\{
    Attributes\RequestDataObject,
    Exceptions\ParameterTypeIsNotAClass,
    RequestFactory
};

readonly class RequestDataObjectResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -30;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): CompiledRequestDataObject|null
    {
        if (!$attribute = attribute(RequestDataObject::class, $parameter)) {
            return null;
        }

        $className = normalizeType($parameter->getType())[0]->getName();

        if (!class_exists($className)) {
            throw new ParameterTypeIsNotAClass($parameter, $className);
        }

        return new CompiledRequestDataObject($attribute, $className);
    }

    /**
     * @param CompiledRequestDataObject $compiled
     */
    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        // Cannot inject via constructor — this resolver is instantiated directly by ArgumentResolver,
        // which is part of the DI bootstrap chain
        $requestData = service(RequestFactory::class)->get();
        $values = [];

        if ($compiled->attribute->fromBody) {
            $values = array_merge($values, $requestData->bodyData->data());
        }

        if ($compiled->attribute->fromQuery) {
            $values = array_merge($values, $requestData->uri->query);
        }

        $object = service(ArrayToObjectCaster::class)->cast($values, $compiled->className);

        return new ParameterResolverResult(true, $object);
    }
}
