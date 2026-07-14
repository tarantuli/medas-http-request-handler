<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Controllers;

use Medas\HttpRequestHandler\{
    Authorization\PublicResource,
    Responses\NoContentResponse,
    ResponseTypes\Response
};
use Medas\Routing\{Methods\Options, Parameters\Anything, Route};

/**
 * Matches OPTIONS requests for every path, so routing has something to resolve to - without a
 * registered route, a preflight request 404s before the response pipeline ever runs, which means
 * CorsHeaderWriter (ResponseModifier) and OptionsHandler (ResponseHandler) never get a chance to
 * act. Both of those already handle origin/method/header validation and write the actual CORS
 * headers regardless of which route matched, so this deliberately does none of that itself -
 * doing so here would just duplicate them.
 */
#[Route(new Anything())]
readonly class OptionsController
{
    #[Options, PublicResource]
    public function getOptions(): Response
    {
        return new NoContentResponse();
    }
}
