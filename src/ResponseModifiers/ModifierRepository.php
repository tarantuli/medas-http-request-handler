<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseModifiers;

use Medas\Core\{Attributes\Service, CachedImplementorList, Lists\SortByPriority};

#[Service]
readonly class ModifierRepository
{
    private CachedImplementorList $modifiers;

    public function __construct()
    {
        $this->modifiers = new CachedImplementorList(
            ResponseModifier::class,
            SortByPriority::HighToLow
        );
    }

    /** @return ResponseModifier[] */
    public function get(): array
    {
        return $this->modifiers->get();
    }
}
