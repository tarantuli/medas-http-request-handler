<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ResponseHandlerRegistry
{
    /** @return ResponseHandlers\ResponseHandler[] */
    public function get(): array
    {
        return cache(__CLASS__, fn() => $this->findHandlers());
    }

    private function findHandlers(): array
    {
        $handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className, $handlers);
        }

        // Sort handlers with the highest priority to the front
        usort(
            $handlers,
            fn(ResponseHandlers\ResponseHandler $a, ResponseHandlers\ResponseHandler $b) =>
                -($a->priority() <=> $b->priority()
            )
        );

        return $handlers;
    }

    private function processClass(string $className, array &$handlers): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(ResponseHandlers\ResponseHandler::class)) {
            return;
        }

        $handlers[] = sm()->resolve($className);
    }
}
