<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Events\AllowedAccess;

class RequestNotAuthorized extends UnauthorizedRequest implements DeclaresResponseCode
{
    public function __construct(
        private readonly AllowedAccess $voteResult,
    )
    {
        parent::__construct($voteResult === AllowedAccess::Denied ? 'disallowed' : 'unauthenticated');
    }

    public function pattern(): string
    {
        return 'this request is %s';
    }

    public function responseCode(): int
    {
        return match ($this->voteResult) {
            AllowedAccess::Denied => 403,
            default => 401,
        };
    }
}
