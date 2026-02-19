<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\HttpRequestHandler\{Request\Request, ResponseTypes\Response};

class Job extends OutputData
{
    public function __construct(
        Request         $request,
        public Response $response,
    )
    {
        parent::__construct($request);
    }
}
