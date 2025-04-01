<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;

#[Service]
class RequestDataManager
{
    private Request\Request $request;

    public function __construct(
        private Request\AuthenticationFinder $authenticationFinder,
    )
    {
    }

    public function __serialize(): array
    {
        // This is needed to make sure $request isn't serialized
        return [];
    }

    public function __unserialize(array $data): void
    {
        // Do nothing
    }

    public function get(): Request\Request
    {
        if (!isset($this->request)) {
            $this->determine();
        }

        return $this->request;
    }

    private function determine(): void
    {
        $this->request = new Request\Request(
            $this->determineMethod(),
            $this->determineEndpoint(),
            new Request\ServerData($_SERVER),
            new Request\PostData($_POST),
            $this->determineBody(),
            new Request\FileData($_FILES),
        );

        if (!isset($this->authenticationFinder)) {
            $this->authenticationFinder = \service(Request\AuthenticationFinder::class);
        }

        $this->authenticationFinder->find($this->request);
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
        $this->request = $request;
    }
}
