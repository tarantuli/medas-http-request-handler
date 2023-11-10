<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;

#[Service]
readonly class RequestFaker
{
    public function __construct(
        private HttpRequestHandler         $requestHandler,
        private Request\RequestDataManager $dataManager,
        private Request\UriManager         $uriManager,
    )
    {
    }

    public function request(
        Request\Method $method,
        string         $uri,
        array          $serverData = [],
        array          $postData = [],
        array          $bodyData = [],
        array          $fileData = [],
    ): void
    {
        $this->dataManager->set(new Request\Request(
            $method,
            $this->uriManager->fromString($uri),
            new Request\ServerData($serverData),
            new Request\PostData($postData),
            new Request\BodyData($bodyData),
            new Request\FileData($fileData),
        ));

        $this->requestHandler->handle();
    }
}
