<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

use Medas\HttpRequestHandler\{Request\Request, ResponseTypes\Response};

class Job extends OutputData
{
    public function __construct(
        public Request  $request,
        public Response $response,
    )
    {
    }
}
