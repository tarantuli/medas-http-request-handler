<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class BodyArgumentIsMissing extends BaseException
{
    public function __construct(string $argumentName)
    {
        parent::__construct($argumentName);
    }

    public function pattern(): string
    {
        return 'body argument %s is missing';
    }
}
