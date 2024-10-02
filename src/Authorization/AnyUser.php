<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authorization;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AnyUser
{
}
