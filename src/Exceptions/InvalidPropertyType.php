<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidPropertyType extends BaseException
{
    public function __construct(string $propertyName, string $expectedType, string $actualType, string $className)
    {
        parent::__construct($propertyName, $className, $expectedType, $actualType);
    }

    public function pattern(): string
    {
        return 'Property %s of class %s must be of type %s, %s given';
    }
}
