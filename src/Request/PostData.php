<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Collections\GenericCollection;

class PostData extends GenericCollection
{
    public function data(): array
    {
        return $this->data;
    }
}
