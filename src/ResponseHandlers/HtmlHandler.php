<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Request\Method,
    Request\Request,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\HtmlResponse
};

#[Service]
class HtmlHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -20;
    }

    public function handleResponse(Job $job): bool
    {
        if (!$job->response instanceof HtmlResponse) {
            return false;
        }

        if (!$this->isHtmlRequest($job->request)) {
            return false;
        }

        $job->response->outputHtmlResponse();

        return true;
    }

    protected function isHtmlRequest(Request $request): bool
    {
        return $request->method === Method::Options
            || $request->serverData->acceptsMimeType('text/html');
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('text/html')) {
            return false;
        }

        printf(
            '<p>%s:%u [%u] %s</p>',
            $job->exception->getFile(),
            $job->exception->getLine(),
            $job->exception->getCode(),
            $job->exception->getMessage()
        );

        return true;
    }
}
