<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\Exceptions\InvalidRequestArgument;
use Medas\ObjectToArraySerializer\{
    ArrayToObjectCaster,
    ArrayToObjectCaster\Settings,
    Exceptions\CantCastValueToType,
    Exceptions\ValueMustBeArray,
    Exceptions\ValueMustBeObject
};

/**
 * Converts request data to the type of the parameter it's injected into: an array into an object
 * of the parameter's class, and -- for query data, where every value is a string -- "3", "2.5" and
 * "true" into an int, float or bool. A value that doesn't fit is an InvalidRequestArgument (a 400),
 * not a server error.
 */
#[Service]
readonly class RequestValueCaster
{
    public function __construct(
        private ArrayToObjectCaster $arrayToObjectCaster,
    )
    {
    }

    public function resolve(mixed $value, CompiledRequestArgument $compiled, bool $coerceStrings): mixed
    {
        if ($compiled->className !== null && is_array($value)) {
            return $this->toObject($value, $compiled->className, $coerceStrings, $compiled->name);
        }

        if ($compiled->scalarType !== null && $coerceStrings && is_string($value)) {
            return $this->toScalar($value, $compiled->scalarType, $compiled->name);
        }

        return $value;
    }

    private function toScalar(string $value, string $type, string $argument): int|float|bool
    {
        $converted = match ($type) {
            'int' => preg_match('#^-?\d+$#', $value) ? (int) $value : null,
            'float' => is_numeric($value) ? (float) $value : null,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
        };

        return $converted ?? throw new InvalidRequestArgument(
            $argument,
            "\"$value\" isn't a valid $type"
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T
     */
    public function toObject(array $values, string $className, bool $coerceStrings, string $argument): object
    {
        try {
            return $this->arrayToObjectCaster->cast(
                $values,
                $className,
                new Settings(skipUnknownValues: true, coerceStrings: $coerceStrings)
            );
        }
        catch (CantCastValueToType|ValueMustBeArray|ValueMustBeObject|\ValueError $exception) {
            throw new InvalidRequestArgument($argument, $exception->getMessage());
        }
    }
}
