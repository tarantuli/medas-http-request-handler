<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\HttpRequestHandler\Exceptions\NotAnHttpRequestException;
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
            new PostData($_POST)
        );
    }

    private function determineMethod(): Method
    {
        return match (true) {
            empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0 => throw new NotAnHttpRequestException(),
            isset($_REQUEST[':method']) => Method::from(strtoupper($_REQUEST[':method'])),
            isset($_SERVER['REQUEST_METHOD']) => Method::from($_SERVER['REQUEST_METHOD']),
        };
    }

    private function determineEndpoint(): Uri
    {
        return $this->uriManager->fromString($_SERVER['REQUEST_URI'] ?? '/');
    }

    public function set(Request $request): void
    {
        $this->request = $request;
    }
}
