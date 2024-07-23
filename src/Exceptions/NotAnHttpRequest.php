<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class NotAnHttpRequest extends BadRequest
{
    public function pattern(): string
    {
        return 'This is not an HTTP request';
    }
}
