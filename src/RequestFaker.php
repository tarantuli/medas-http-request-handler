<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Request\{Method, PostData, Request, RequestDataManager, ServerData, UriManager};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequestFaker
{
    public function __construct(
        private HttpRequestHandler $requestHandler,
        private RequestDataManager $dataManager,
        private UriManager         $uriManager,
    )
    {
    }

    public function request(Method $method, string $uri, array $serverData = [], array $postData = []): void
    {
        $this->dataManager->set(new Request(
            $method,
            $this->uriManager->fromString($uri),
            new ServerData($serverData),
            new PostData($postData)
        ));

        $this->requestHandler->handle();
    }
}
