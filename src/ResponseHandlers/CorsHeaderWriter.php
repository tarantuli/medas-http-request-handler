<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HttpRequestHandler\{
    ConfigOptions\CorsAllowedOrigins,
    ConfigOptions\CorsMaxAge,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job
};

#[Service]
readonly class CorsHeaderWriter
{
    public function __construct(
        #[ConfigValue(CorsAllowedOrigins::class)]
        private string $allowedOrigins,

        #[ConfigValue(CorsMaxAge::class)]
        private int    $maxAge,
    )
    {
    }

    public function handle(Job|ExceptionJob $job): void
    {
        if ($this->allowedOrigins === '') {
            return;
        }

        $requestOrigin = $job->request->serverData['HTTP_ORIGIN'] ?? '';

        if ($this->allowedOrigins === '*') {
            $this->addHeaders($requestOrigin, $job);

            return;
        }

        $allowedOrigins = array_map('trim', explode(',', $this->allowedOrigins));

        if (in_array($requestOrigin, $allowedOrigins, true)) {
            $this->addHeaders($requestOrigin, $job);
        }
    }

    private function addHeaders(mixed $requestOrigin, Job|ExceptionJob $job): void
    {
        $job->headers['Access-Control-Allow-Origin'] = $requestOrigin;
        $job->headers['Access-Control-Allow-Credentials'] = 'true';

        if ($this->maxAge > 0) {
            $job->headers['Access-Control-Max-Age'] = (string) $this->maxAge;
        }
    }
}
