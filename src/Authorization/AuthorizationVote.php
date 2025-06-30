<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authorization;

use Medas\Core\{Events\BasicVote, Interfaces\HttpRequestHandler};
use Medas\HttpRequestHandler\Request\Request;

class AuthorizationVote extends BasicVote
{
    public function __construct(
        public readonly Request            $request,
        public readonly HttpRequestHandler $requestHandler,
    )
    {
    }
}
