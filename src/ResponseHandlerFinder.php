<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager};

#[Service]
class ResponseHandlerFinder
{
    /** @var ResponseHandlers\ResponseHandler[] $handlers */
    private array $handlers;

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    /** @return ResponseHandlers\ResponseHandler[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get(
            [static::class, 'getHandlers'],
            fn() => $this->findHandlers()
        );
    }

    private function findHandlers(): array
    {
        $this->handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className);
        }

        // Sort handlers with the highest priority to the front
        usort(
            $this->handlers,
            fn(ResponseHandlers\ResponseHandler $a, ResponseHandlers\ResponseHandler $b) =>
                -($a->priority() <=> $b->priority())
        );

        return $this->handlers;
    }

    private function processClass(string $className): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(ResponseHandlers\ResponseHandler::class)) {
            return;
        }

        $this->handlers[] = sm()->resolve($className);
    }
}
