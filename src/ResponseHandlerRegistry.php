<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager};
use Medas\ServiceManager\ServiceManager;

#[Service]
readonly class ResponseHandlerRegistry
{
    public function __construct(
        private CacheManager   $cacheManager,
        private ServiceManager $serviceManager,
    )
    {
    }

    /** @return ResponseHandlers\ResponseHandler[] */
    public function get(): array
    {
        $classNames = $this->cacheManager->get()->get(__CLASS__, fn() => $this->gatherClassNames());

        return namesToServices($classNames);
    }

    /** @return string[] */
    private function gatherClassNames(): array
    {
        /** @var ResponseHandlers\ResponseHandler[] $handlers */
        $handlers = [];

        foreach ($this->serviceManager->getServiceClassNames() as $className) {
            $this->processClass($className, $handlers);
        }

        // Sort handlers with the highest priority to the front
        usort(
            $handlers,
            fn(ResponseHandlers\ResponseHandler $a, ResponseHandlers\ResponseHandler $b) =>
                -($a->priority() <=> $b->priority()
            )
        );

        return servicesToNames($handlers);
    }

    /** @param ResponseHandlers\ResponseHandler[] $handlers */
    private function processClass(string $className, array &$handlers): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(ResponseHandlers\ResponseHandler::class)) {
            return;
        }

        $handlers[] = $this->serviceManager->resolve($className);
    }
}
