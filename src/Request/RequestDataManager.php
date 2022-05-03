<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\HttpRequestHandler\Exceptions\NotAnHttpRequestException;
use Medas\HttpRequestHandler\Exceptions\UnknownMethodException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequestDataManager
{
    private Request $request;

    public function __construct(
        private readonly UriManager $uriManager,
    )
    {
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
        );
    }

    private function determineMethod(): Method
    {
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            throw new NotAnHttpRequestException();
        }

        $name = $_REQUEST['::method'] ?? $_SERVER['REQUEST_METHOD'] ?? null;

        try {
            return Method::from($name);
        }
        catch (\ValueError) {
            throw new UnknownMethodException($name);
        }
    }

    private function determineEndpoint(): Uri
    {
        return $this->uriManager->fromString($_SERVER['REQUEST_URI'] ?? '/');
    }

    public function set(Request $request): void
    {
        $this->request = $request;
    }

    private function determineBody(): BodyData
    {
        $raw = file_get_contents('php://input');

        if ($raw === '') {
            $body = [];
        }
        elseif (str_contains($_SERVER['HTTP_CONTENT_TYPE'] ?? '', 'application/json')) {
            $body = json_decode($raw, true);
        }
        elseif (str_contains($_SERVER['HTTP_CONTENT_TYPE'] ?? '', 'application/json')) {
            parse_str($raw, $body);
        }
        else {
            throw new \Exception('cannot determine body values from ' . $raw);
        }

        return new BodyData($body);
    }
}
