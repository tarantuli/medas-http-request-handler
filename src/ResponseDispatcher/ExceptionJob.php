<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\HttpRequestHandler\Request\Request;

class ExceptionJob extends OutputData
{
    public function __construct(
        Request           $request,
        public \Throwable $exception,
    )
    {
        parent::__construct($request);
    }
}
