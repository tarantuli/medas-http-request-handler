<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\Request\{BodyData,
    FileData,
    Method,
    PostData,
    Request,
    RequestDataManager,
    ServerData,
    UriManager};

#[Service]
readonly class RequestFaker
{
    public function __construct(
        private HttpRequestHandler $requestHandler,
        private RequestDataManager $dataManager,
        private UriManager         $uriManager,
    )
    {
    }

    public function request(Method $method,
                            string $uri,
                            array  $serverData = [],
                            array  $postData = [],
                            array  $bodyData = [],
                            array  $fileData = [],
    ): void
    {
        $this->dataManager->set(new Request(
            $method,
            $this->uriManager->fromString($uri),
            new ServerData($serverData),
            new PostData($postData),
            new BodyData($bodyData),
            new FileData($fileData),
        ));

        $this->requestHandler->handle();
    }
}
