<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HttpRequestHandler\{
    ConfigOptions\CorsAllowedOrigins,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job
};

#[Service]
readonly class CorsHeaderWriter
{
    public function __construct(
        #[ConfigValue(CorsAllowedOrigins::class)]
        private string $corsAllowedOrigins,
    )
    {
    }

    public function handle(Job|ExceptionJob $job): void
    {
        if ($this->corsAllowedOrigins === '') {
            return;
        }

        $requestOrigin = $job->request->serverData['HTTP_ORIGIN'] ?? '';

        if ($this->corsAllowedOrigins === '*') {
            $job->headers['Access-Control-Allow-Origin'] = $requestOrigin;
            $job->headers['Access-Control-Allow-Credentials'] = 'true';

            return;
        }

        $allowedOrigins = array_map('trim', explode(',', $this->corsAllowedOrigins));

        if (in_array($requestOrigin, $allowedOrigins, true)) {
            $job->headers['Access-Control-Allow-Origin'] = $requestOrigin;
            $job->headers['Access-Control-Allow-Credentials'] = 'true';
        }
    }
}
