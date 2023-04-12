<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\ResponseHandlers\ResponseHandler;
use Medas\ServiceManager\Cache\CacheManager;
use Medas\ServiceManager\Service;

#[Service]
class ResponseHandlerFinder
{
    /** @var ResponseHandler[] $handlers */
    private array $handlers;

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    /** @return ResponseHandler[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get([static::class, 'getHandlers'], fn() => $this->findHandlers());
    }

    private function findHandlers(): array
    {
        $this->handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className);
        }

        // Sort handlers with the highest priority to the front
        usort($this->handlers, fn(ResponseHandler $a, ResponseHandler $b) => -($a->priority() <=> $b->priority()));

        return $this->handlers;
    }

    private function processClass(string $className): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(ResponseHandler::class)) {
            return;
        }

        $this->handlers[] = sm()->resolve($className);
    }
}
