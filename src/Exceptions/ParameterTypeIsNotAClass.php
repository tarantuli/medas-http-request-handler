<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ParameterTypeIsNotAClass extends BaseException
{
    public function __construct(\ReflectionParameter|\ReflectionProperty $parameter, mixed $type)
    {
        parent::__construct(
            $parameter->name,
            $parameter->getDeclaringClass()->name,
            $parameter->getDeclaringFunction()->name,
            $type
        );
    }

    public function pattern(): string
    {
        return 'parameter %s of %s->%s() must be a type of class, it\'s %s instead';
    }
}
