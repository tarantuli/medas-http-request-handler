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
 * This authorization vote handler checks if the route handler method or its declaring class are tagged with AnyUser.
 * If so, then access is allowed if any user is authenticated. If not, access is disallowed.
 */
#[Service]
readonly class AnyUserHandler
{
    #[EventListener]
    public function handleAuthorizationVote(AuthorizationVote $vote): void
    {
        if (!$vote->requestHandler instanceof HttpRequestHandlerDefersToMethod) {
            return;
        }

        $methodReflector = $vote->requestHandler->handlerMethod();

        if (attribute(AnyUser::class, $methodReflector)
                || attribute(AnyUser::class, $methodReflector->getDeclaringClass())) {
            $vote->allowedAccess = $vote->request->authentication->user !== null
                ? AllowedAccess::Allowed
                : AllowedAccess::Denied;
        }
    }
}
