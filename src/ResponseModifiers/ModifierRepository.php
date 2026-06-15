<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseModifiers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class ModifierRepository
{
    public function __construct(
        private CacheManager      $cacheManager,
        private ImplementorFinder $implementorFinder,
    )
    {
    }

    /** @return ResponseModifier[] */
    public function get(): array
    {
        $classNames = $this->cacheManager->get()->get(__CLASS__, fn() => $this->gatherClassNames());

        return namesToServices($classNames);
    }

    /** @return string[] */
    private function gatherClassNames(): array
    {
        $names = $this->implementorFinder->find(ResponseModifier::class);
        $classes = namesToServices($names);

        usort($classes, fn($a, $b) => -($a->priority() <=> $b->priority()));

        return servicesToNames($classes);
    }
}
