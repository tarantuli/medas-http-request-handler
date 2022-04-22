<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

class PostData
{
    public function __construct(
        private readonly array $data
    )
    {
    }

    public function data(): array
    {
        return $this->data;
    }
}
