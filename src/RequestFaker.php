<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Request\{BodyData, Method, PostData, Request, RequestDataManager, ServerData, UriManager};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequestFaker
{
    public function __construct(
        private readonly HttpRequestHandler $requestHandler,
        private readonly RequestDataManager $dataManager,
        private readonly UriManager         $uriManager,
    )
    {
    }

    public function request(Method $method, string $uri, array $serverData = [], array $postData = [], array $bodyData = []): void
    {
        $this->dataManager->set(new Request(
            $method,
            $this->uriManager->fromString($uri),
            new ServerData($serverData),
            new PostData($postData),
            new BodyData($bodyData),
        ));

        $this->requestHandler->handle();
    }
}
