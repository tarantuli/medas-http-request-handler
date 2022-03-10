<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\HttpRequestHandler\ResponseTypes\Response;

class NoJsonResponseException extends BaseException
{
    public function __construct(Response $response)
    {
        parent::__construct($response::class);
    }

    public function pattern(): string
    {
        return 'JSON response requested, but %s does not implement JsonResponse';
    }
}
