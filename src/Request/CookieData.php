<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Collections\GenericCollection;

class CookieData extends GenericCollection
{
    public function data(): array
    {
        return $this->data;
    }
}
