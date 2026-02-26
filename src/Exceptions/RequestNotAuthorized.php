<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Events\AllowedAccess;

class RequestNotAuthorized extends UnauthorizedRequest
{
    public function __construct(AllowedAccess $voteResult)
    {
        parent::__construct($voteResult === AllowedAccess::Denied ? 'disallowed' : 'unauthorized');
    }

    public function pattern(): string
    {
        return 'this request is %s';
    }
}
