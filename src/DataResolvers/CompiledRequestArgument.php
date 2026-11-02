<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

/**
 * The compiled form of a parameter that @class(BodyDataResolver) or @class(QueryDataResolver)
 * resolves: the argument's name, and what its value is converted to (see
 * @class(RequestValueCaster)).
 */
readonly class CompiledRequestArgument
{
    private const array SCALAR_TYPES = ['int', 'float', 'bool'];

    public static function fromParameter(string $name, \ReflectionParameter|\ReflectionProperty $parameter): self
    {
        $types = normalizeType($parameter->getType());
        $typeName = $types ? $types[0]->getName() : null;

        if ($typeName === null) {
            return new self($name);
        }

        if (in_array($typeName, self::SCALAR_TYPES, true)) {
            return new self($name, scalarType: $typeName);
        }

        // Enums and dates come as strings and are converted elsewhere
        $isCastableClass = class_exists($typeName)
            && !enum_exists($typeName)
            && !is_a($typeName, \DateTimeInterface::class, true);

        return new self($name, className: $isCastableClass ? $typeName : null);
    }

    public function __construct(
        public string      $name,

        // A class an array value is cast into
        public string|null $className = null,

        // int, float or bool, which a string from the query is converted to
        public string|null $scalarType = null,
    )
    {
    }
}
