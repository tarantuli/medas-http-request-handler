<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class RequestDataMutationOutsideOfCli extends BaseException
{
    public function pattern(): string
    {
        return 'request data mutation from outside of cli';
    }
}
