<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Request\Method,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job
};

#[Service]
readonly class OptionsHandler implements ResponseHandler
{
    public function priority(): int
    {
        return 0;
    }

    public function handleResponse(Job $job): bool
    {
        if ($job->request->method !== Method::Options) {
            return false;
        }

        $job->setHeader(
            'Access-Control-Allow-Methods',
            implode(', ', array_column(Method::cases(), 'value'))
        );

        $requestedHeaders = $job->request->serverData['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';

        if ($requestedHeaders !== '') {
            // Prevents a crafted Access-Control-Request-Headers value from
            // splitting the response into extra headers - browsers
            // themselves constrain this value, but nothing stops a
            // non-browser client from sending an arbitrary one directly.
            $requestedHeaders = str_replace(["\r", "\n", "\0"], '', $requestedHeaders);

            $job->setHeader('Access-Control-Allow-Headers', $requestedHeaders);
        }

        $job->responseCode = 204;

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        return false;
    }
}
