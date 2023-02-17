<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NotAnHttpRequest extends BaseException
{
    public function pattern(): string
    {
        return 'This is not an HTTP request';
    }
}
