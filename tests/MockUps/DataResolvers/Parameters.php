<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\MockUps\DataResolvers;

/**
 * Only its constructor's parameters are used, as the parameters the resolvers compile
 */
class Parameters
{
    public static function parameter(string $name): \ReflectionParameter
    {
        return new \ReflectionParameter([self::class, '__construct'], $name);
    }

    public function __construct(
        SearchParameters   $search,
        int                $limit,
        bool               $draft,
        string             $name,
        array              $list,
        \DateTimeImmutable $date,
    )
    {
    }
}
