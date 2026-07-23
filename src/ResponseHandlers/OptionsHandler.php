<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\{Attributes\Service, Cors\CorsHeaders};
use Medas\HttpRequestHandler\{
    Request\Method,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job
};

#[Service]
readonly class OptionsHandler implements ResponseHandler
{
    public function __construct(
        private CorsHeaders $corsHeaders,
    )
    {
    }

    public function priority(): int
    {
        return 0;
    }

    public function handleResponse(Job $job): bool
    {
        if ($job->request->method !== Method::Options) {
            return false;
        }

        $requestedHeaders = $job->request->serverData['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';

        $headers = $this->corsHeaders->resolvePreflightHeaders(
            array_column(Method::cases(), 'value'),
            $requestedHeaders,
        );

        foreach ($headers as $name => $value) {
            $job->setHeader($name, $value);
        }

        $job->responseCode = 204;

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        return false;
    }
}
