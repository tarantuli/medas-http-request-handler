<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;

#[Service]
readonly class RequestDataManager
{
    public function __construct(
        private Request\AuthenticationFinder $authenticationFinder,
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

    private function determineMethod(): Request\Method
    {
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            throw new Exceptions\NotAnHttpRequest();
        }

        $name = $_REQUEST['::method'] ?? $_SERVER['REQUEST_METHOD'] ?? null;

        try {
            return Request\Method::from($name);
        }
        catch (\ValueError) {
            throw new Exceptions\UnknownMethod($name);
        }
    }

    private function determineEndpoint(): Request\Uri
    {
        return service(Request\UriManager::class)->fromString($_SERVER['REQUEST_URI'] ?? '/');
    }

    private function determineBody(): Request\BodyData
    {
        $raw = file_get_contents('php://input');

        if ($raw === '') {
            $body = [];
        }
        elseif (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $body = json_decode($raw, true);
        }
        elseif (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/x-www-form-urlencoded')) {
            parse_str($raw, $body);
        }
        else {
            throw new \Exception('cannot determine body values from ' . $raw);
        }

        return new Request\BodyData($body);
    }

    public function set(Request\Request $request): void
    {
        cacheSet(self::class, $request, 'memory');
    }
}
