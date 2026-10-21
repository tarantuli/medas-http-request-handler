<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\Core\{Attributes\Service, Exceptions\BaseException, StringMaker};
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

    public function normalize(\Throwable $exception, int $responseCode = 500): array
    {
        $normalized = $this->throwableNormalizer->normalize($exception);

        if (!$this->isDev) {
            $normalized = array_intersect_key($normalized, self::ALWAYS_VISIBLE);
        }

        // A refusal (4xx) also carries a message identifier and the values its message was
        // made from, so a client can show it in its own language. A server error has
        // nothing for the user to act on and gets neither.
        if ($exception instanceof BaseException && $responseCode >= 400 && $responseCode < 500) {
            $normalized['messageIdentifier'] = $exception->messageIdentifier();

            $normalized['messageParameters'] = array_map(
                $this->parameterForClient(...),
                $exception->parameters()
            );
        }

        return $normalized;
    }

    // Scalars go as they are; anything else as the text it has in the message.
    private function parameterForClient(mixed $parameter): string|int|float|bool|null
    {
        if ($parameter === null || is_scalar($parameter)) {
            return $parameter;
        }

        return StringMaker::instance()->fromVariable($parameter);
    }
}
