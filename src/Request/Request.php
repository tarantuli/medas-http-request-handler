<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\HttpRequestHandler\Authentication\Authentication;

readonly class Request
{
    public Authentication $authentication;

    public function __construct(
        public Method     $method,
        public Uri        $uri,
        public ServerData $serverData,
        public PostData   $postData,
        public BodyData   $bodyData,
        public FileData   $fileData,
    )
    {
        $this->authentication = new Authentication();
    }
}
