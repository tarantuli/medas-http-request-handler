<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

class Request
{
    public function __construct(
        public Method     $method,
        public Uri        $uri,
        public ServerData $serverData,
        public PostData   $postData,
        public BodyData   $bodyData,
    )
    {
    }
}
