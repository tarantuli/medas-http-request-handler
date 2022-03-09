<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidHostStringException extends BaseException
{
    public function __construct(string $host)
    {
        parent::__construct($host);
    }

    public function pattern(): string
    {
        return 'Invalid host string %s';
    }
}
