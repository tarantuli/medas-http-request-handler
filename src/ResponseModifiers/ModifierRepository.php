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
        return $this->cacheManager->get()->get(__CLASS__, fn() => $this->gatherInstances());
    }

    /** @return ResponseModifier[] */
    private function gatherInstances(): array
    {
        $implementors = $this->implementorFinder->find(ResponseModifier::class);

        usort($implementors, fn($a, $b) => -($a->priority() <=> $b->priority()));

        return $implementors;
    }
}
