<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authorization;

use Medas\Core\{
    Attributes\EventListener,
    Attributes\Service,
    Events\AllowedAccess,
    Interfaces\HttpRequestHandlerDefersToMethod
};

/**
 * This authorisation vote handler checks if the route handler method or its declaring class are tagged with PublicResource.
 * If so, then access is allowed. In all other cases, it expresses no opinion.
 *
 * It runs after the other vote handlers, so an application's path-based handlers (e.g. "everything under /items needs
 * a user") can't overrule a public resource by voting Unauthenticated - which made CORS preflight requests, matched by
 * OptionsController and never carrying credentials, fail with 401. An explicit Denied still stops the vote before
 * this runs.
 */
#[Service]
readonly class PublicResourceHandler
{
    #[EventListener(priority: -1000)]
    public function handleAuthorizationVote(AuthorizationVote $vote): void
    {
        if (!$vote->requestHandler instanceof HttpRequestHandlerDefersToMethod) {
            return;
        }

        $methodReflector = $vote->requestHandler->handlerMethod();

        if (attribute(PublicResource::class, $methodReflector)
                || attribute(PublicResource::class, $methodReflector->getDeclaringClass())) {
            $vote->allowedAccess = AllowedAccess::Allowed;
        }
    }
}
