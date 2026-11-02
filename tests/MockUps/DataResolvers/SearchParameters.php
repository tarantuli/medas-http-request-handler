<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\MockUps\DataResolvers;

class SearchParameters
{
    public string|null $artist = null;

    /** @var int[] */
    public array $tags = [];

    public bool|null $isFavorite = null;
}
