<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Request\Request,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\HtmlResponse
};

#[Service]
readonly class HtmlHandler implements ResponseHandler
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

        $job->headers['Content-Type'] = 'text/html; charset=utf-8';
        $job->output = $job->response->getHtmlResponse();

        return true;
    }

    protected function isHtmlRequest(Request $request): bool
    {
        return $request->serverData->acceptsMimeType('text/html');
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('text/html')) {
            return false;
        }

        $job->headers['Content-Type'] = 'text/html; charset=utf-8';

        $job->output = sprintf(
            '<p>%s:%u [%u] %s</p>',
            htmlspecialchars($job->exception->getFile()),
            $job->exception->getLine(),
            $job->exception->getCode(),
            htmlspecialchars($job->exception->getMessage())
        );

        return true;
    }
}
