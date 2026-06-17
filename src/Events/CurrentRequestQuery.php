<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Events;

use Medas\HttpRequestHandler\Request\Request;

class CurrentRequestQuery
{
    public Request $request;
}
