<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Cors\CorsHeaders};
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
        string|array        $allowedOrigins,

        #[ConfigValue(CorsMaxAge::class)]
        private int         $maxAge,
        private CorsHeaders $corsHeaders,
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
        $requestOrigin = $job->request->serverData['HTTP_ORIGIN'] ?? '';

        $headers = $this->corsHeaders->resolveResponseHeaders(
            $this->allowedOrigins,
            $requestOrigin,
            $this->maxAge,
            ['X-Total-Count'],
        );

        foreach ($headers as $name => $value) {
            $job->setHeader($name, $value);
        }
    }
}
