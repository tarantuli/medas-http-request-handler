<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\HttpRequestHandler\ResponseTypes\Response;

class CannotHandleResponseType extends BaseException
{
    public function __construct(Response $response)
    {
        parent::__construct($response::class);
    }

    public function pattern(): string
    {
        return 'No handler found that can process a response of type %s for this request';
    }
}
