<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\HttpRequestHandler\ResponseTypes\Response;

class CannotHandleResponseException extends BaseException
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
    }

    public function pattern(): string
    {
        return 'No handler found for response %s';
    }
}
