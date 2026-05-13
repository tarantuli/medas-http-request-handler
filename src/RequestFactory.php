<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\Json\JsonEncoder;

#[Service]
readonly class RequestFactory
{
    public function __construct(
        private JsonEncoder                  $jsonEncoder,
        private Request\AuthenticationFinder $authenticationFinder,
        private Request\UriManager           $uriManager,

        #[ConfigValue(ConfigOptions\MaxPayloadSize::class)]
        private int                          $maxPayloadSize
    )
    {
    }

    public function get(): Request\Request
    {
        return cache(self::class, fn() => $this->determine(), 'memory');
    }

    private function determine(): Request\Request
    {
        $request = new Request\Request(
            $this->determineMethod(),
            $this->determineEndpoint(),
            new Request\ServerData($_SERVER),
            new Request\PostData($_POST),
            $this->determineBody(),
            new Request\FileData($_FILES),
        );

        $this->authenticationFinder->find($request);

        return $request;
    }

    public function getWithoutExceptions(): Request\Request
    {
        try {
            $method = $this->determineMethod();
        }
        catch (\Throwable) {
            $method = Request\Method::Get;
        }

        try {
            $uri = $this->determineEndpoint();
        }
        catch (\Throwable) {
            $uri = $this->uriManager->fromString('/unknown');
        }

        try {
            $body = $this->determineBody();
        }
        catch (\Throwable) {
            $body = new Request\BodyData([]);
        }

        return new Request\Request(
            $method,
            $uri,
            new Request\ServerData($_SERVER),
            new Request\PostData($_POST),
            $body,
            new Request\FileData($_FILES),
        );
    }

    /**
     * If $_REQUEST contains an entry named '::method', it will be used as the request method.
     * Otherwise, $_SERVER['REQUEST_METHOD'] will be used.
     */
    private function determineMethod(): Request\Method
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new Exceptions\NotAnHttpRequest();
        }

        $name = $_REQUEST['::method'] ?? $_SERVER['REQUEST_METHOD'];

        try {
            return Request\Method::from($name);
        }
        catch (\ValueError) {
            throw new Exceptions\UnknownMethod($name);
        }
    }

    private function determineEndpoint(): Request\Uri
    {
        return $this->uriManager->fromString($_SERVER['REQUEST_URI'] ?? '/');
    }

    private function determineBody(): Request\BodyData
    {
        // Define max body size (e.g., 10MB)
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

        if ($contentLength > $this->maxPayloadSize) {
            throw new Exceptions\PayloadTooLarge($contentLength, $this->maxPayloadSize);
        }

        // Read with limit
        $resource = fopen('php://input', 'r');
        $raw = stream_get_contents($resource, $this->maxPayloadSize);

        fclose($resource);

        if ($raw === false) {
            throw new Exceptions\FailedReadRequestBody();
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if ($raw === '') {
            $body = [];
        }
        elseif (str_contains($contentType, 'application/json')) {
            try {
                $body = $this->jsonEncoder->decode($raw);
            }
            catch (\JsonException) {
                throw new Exceptions\InvalidJsonBody($raw);
            }
        }
        elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($raw, $body);
        }
        elseif (str_contains($contentType, 'multipart/form-data')) {
            $body = $_POST;
        }
        else {
            throw new Exceptions\UnsupportedContentType($contentType);
        }

        return new Request\BodyData($body);
    }

    /**
     * This method should only be called by test scripts to set and test specific requests
     */
    public function set(Request\Request $request): void
    {
        if (PHP_SAPI !== 'cli') {
            throw new Exceptions\RequestDataMutationOutsideOfCli();
        }

        cacheSet(self::class, $request, 'memory');
    }
}
