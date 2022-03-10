<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Request\Method;
use Medas\HttpRequestHandler\Request\PostData;
use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\Request\RequestDataManager;
use Medas\HttpRequestHandler\Request\ServerData;
use Medas\HttpRequestHandler\Request\UriManager;
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

    public function request(Method $method, string $uri, array $serverData = [], array $postData = []): string
    {
        $this->dataManager->set(new Request(
            $method,
            $this->uriManager->fromString($uri),
            new ServerData($serverData),
            new PostData($postData)
        ));

        ob_start();
        $this->requestHandler->handle();
        return ob_get_clean();
    }
}
