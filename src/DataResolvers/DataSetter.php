<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\Exceptions\InvalidPropertyType;

#[Service]
readonly class DataSetter
{
    public function set(object $object, array $data): void
    {
        $reflection = new \ReflectionObject($object);

        foreach ($data as $name => $value) {
            if (!$reflection->hasProperty($name)) {
                continue;
            }

            $property = $reflection->getProperty($name);

            // Skip private/protected properties
            if (!$property->isPublic()) {
                continue;
            }

            // Skip readonly properties
            if ($property->isReadOnly()) {
                continue;
            }

            // Validate type
            $type = $property->getType();

            if ($type && !$this->isValidType($value, $type)) {
                throw new InvalidPropertyType(
                    propertyName: $name,
                    expectedType: $type instanceof \ReflectionNamedType
                        ? $type->getName()
                        : (string) $type,
                    actualType: get_debug_type($value),
                    className: $object::class
                );
            }

            $property->setValue($object, $value);
        }
    }

    private function isValidType(mixed $value, \ReflectionType $type): bool
    {
        if ($type->allowsNull() && $value === null) {
            return true;
        }

        if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();

            // Check built-in types
            return match ($typeName) {
                'int' => is_int($value),
                'float' => is_float($value) || is_int($value),
                'string' => is_string($value),
                'bool' => is_bool($value),
                'array' => is_array($value),
                default => $value instanceof $typeName,
            };
        }

        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($this->isValidType($value, $unionType)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
}
