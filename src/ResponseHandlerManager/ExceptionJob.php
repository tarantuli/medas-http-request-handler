<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

use Medas\HttpRequestHandler\Request\Request;

class ExceptionJob extends OutputData
{
    public function __construct(
        public Request    $request,
        public \Throwable $exception,
    )
    {
    }
}
