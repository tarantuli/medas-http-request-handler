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
 * This authorization vote handler checks if the route handler method or its declaring class are tagged with PublicResource.
 * If so, then access is allowed. In all other cases, it expresses no opinion.
 */
#[Service]
readonly class PublicResourceHandler
{
    #[EventListener]
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
