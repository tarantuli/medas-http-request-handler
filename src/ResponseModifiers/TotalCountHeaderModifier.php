<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseModifiers;

use Medas\Core\{Attributes\Service, Interfaces\HasTotalCount};
use Medas\HttpRequestHandler\ResponseDispatcher\{ExceptionJob, Job};

#[Service]
readonly class TotalCountHeaderModifier implements ResponseModifier
{
    public function handle(Job|ExceptionJob $job): void
    {
        if ($job instanceof Job && $job->response instanceof HasTotalCount) {
            $job->setHeader('X-Total-Count', (string) $job->response->totalCount());
        }
    }

    public function priority(): int
    {
        return 0;
    }
}
