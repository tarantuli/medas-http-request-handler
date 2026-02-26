<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\HttpRequestHandler\Authentication\Authentication;

readonly class Request
{
    public function __construct(
        public Method         $method,
        public Uri            $uri,
        public ServerData     $serverData,
        public PostData       $postData,
        public BodyData       $bodyData,
        public FileData       $fileData,
        public Authentication $authentication = new Authentication(),
    )
    {
    }
}
