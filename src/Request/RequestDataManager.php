<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\HttpRequestHandler\Exceptions\{NotAnHttpRequest, UnknownMethod};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequestDataManager
{
    private Request $request;

    public function __serialize(): array
    {
        // This is needed to make sure $request isn't serialized
        return [];
    }

    public function __unserialize(array $data): void
    {
        // Do nothing
    }

    public function get(): Request
    {
        if (!isset($this->request)) {
            $this->determine();
        }

        return $this->request;
    }

    private function determine(): void
    {
        $this->request = new Request(
            $this->determineMethod(),
            $this->determineEndpoint(),
            new ServerData($_SERVER),
            new PostData($_POST),
            $this->determineBody(),
            new FileData($_FILES),
        );
    }

    private function determineMethod(): Method
    {
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            throw new NotAnHttpRequest();
        }

        $name = $_REQUEST['::method'] ?? $_SERVER['REQUEST_METHOD'] ?? null;

        try {
            return Method::from($name);
        }
        catch (\ValueError) {
            throw new UnknownMethod($name);
        }
    }

    private function determineEndpoint(): Uri
    {
        return service(UriManager::class)->fromString($_SERVER['REQUEST_URI'] ?? '/');
    }

    private function determineBody(): BodyData
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

        return new BodyData($body);
    }

    public function set(Request $request): void
    {
        $this->request = $request;
    }
}
