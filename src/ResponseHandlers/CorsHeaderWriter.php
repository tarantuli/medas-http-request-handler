<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HttpRequestHandler\{
    ConfigOptions\CorsAllowedOrigins,
    ConfigOptions\CorsMaxAge,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseModifiers\ResponseModifier
};

#[Service]
readonly class CorsHeaderWriter implements ResponseModifier
{
    private array $allowedOrigins;

    public function __construct(
        #[ConfigValue(CorsAllowedOrigins::class)]
        string|array $allowedOrigins,

        #[ConfigValue(CorsMaxAge::class)]
        private int  $maxAge,
    )
    {
        if (is_string($allowedOrigins)) {
            $this->allowedOrigins = array_map('trim', explode(',', $allowedOrigins));
        }
        else {
            $this->allowedOrigins = $allowedOrigins;
        }
    }

    public function priority(): int
    {
        return 0;
    }

    public function handle(Job|ExceptionJob $job): void
    {
        if ($this->allowedOrigins === []) {
            return;
        }

        $requestOrigin = $job->request->serverData['HTTP_ORIGIN'] ?? '';

        if (in_array('*', $this->allowedOrigins, true)) {
            $this->addHeaders($requestOrigin, $job);

            return;
        }

        if (in_array($requestOrigin, $this->allowedOrigins, true)) {
            $this->addHeaders($requestOrigin, $job);
        }
    }

    private function addHeaders(mixed $requestOrigin, Job|ExceptionJob $job): void
    {
        // Strips control characters before reflecting the origin back into a
        // response header - relevant in wildcard mode specifically, where
        // any origin is accepted verbatim rather than checked against a
        // pre-configured, already-trusted allowlist. Prevents a crafted
        // Origin value (e.g., containing \r\n) from splitting the response
        // into extra headers.
        $requestOrigin = str_replace(["\r", "\n", "\0"], '', $requestOrigin);

        $job->setHeader('Access-Control-Allow-Origin', $requestOrigin);
        $job->setHeader('Access-Control-Allow-Credentials', 'true');
        $job->setHeader('Access-Control-Expose-Headers', 'X-Total-Count');

        if ($this->maxAge > 0) {
            $job->setHeader('Access-Control-Max-Age', (string) $this->maxAge);
        }
    }
}
