<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class BadRequest extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function pattern(): string
    {
        return 'Bad request: %s';
    }
}
