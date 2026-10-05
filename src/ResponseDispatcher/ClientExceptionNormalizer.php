<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\Core\Attributes\Service;
use Medas\ErrorReporting\Normalizing\ThrowableNormalizer;

/**
 * Decides what a client gets to see of an exception. The file name, line number,
 * and trace describe the server's internals, so they are only sent in dev; the
 * exception log and mail carry them in every environment.
 */
#[Service]
readonly class ClientExceptionNormalizer
{
    private const array ALWAYS_VISIBLE = ['message' => true, 'code' => true];

    // isDev is bound by HttpRequestHandlerPackage; it defaults to the safe side.
    public function __construct(
        private ThrowableNormalizer $throwableNormalizer,
        private bool                $isDev = false,
    )
    {
    }

    public function normalize(\Throwable $exception): array
    {
        $normalized = $this->throwableNormalizer->normalize($exception);

        return $this->isDev ? $normalized : array_intersect_key($normalized, self::ALWAYS_VISIBLE);
    }
}
