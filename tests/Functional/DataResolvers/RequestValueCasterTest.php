<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional\DataResolvers;

use Medas\HttpRequestHandler\{
    DataResolvers\CompiledRequestArgument,
    DataResolvers\RequestValueCaster,
    Exceptions\InvalidRequestArgument
};
use Medas\HttpRequestHandlerTest\MockUps\DataResolvers\{Parameters, SearchParameters};
use PHPUnit\Framework\TestCase;

class RequestValueCasterTest extends TestCase
{
    public function testCompiling(): void
    {
        $search = CompiledRequestArgument::fromParameter('search', Parameters::parameter('search'));

        self::assertSame(SearchParameters::class, $search->className);
        self::assertNull($search->scalarType);

        $limit = CompiledRequestArgument::fromParameter('limit', Parameters::parameter('limit'));

        self::assertSame('int', $limit->scalarType);

        // Dates and plain types are left to other argument processors
        $date = CompiledRequestArgument::fromParameter('date', Parameters::parameter('date'));
        $name = CompiledRequestArgument::fromParameter('name', Parameters::parameter('name'));

        self::assertNull($date->className);
        self::assertNull($name->className);
        self::assertNull($name->scalarType);
    }

    public function testQueryArrayBecomesObject(): void
    {
        $compiled = CompiledRequestArgument::fromParameter(
            'search',
            Parameters::parameter('search')
        );

        /** @var SearchParameters $search */
        $search = service(RequestValueCaster::class)->resolve(
            ['artist' => 'Queen', 'tags' => ['3', '5'], 'isFavorite' => 'true', 'unknown' => 'x'],
            $compiled,
            coerceStrings: true
        );

        self::assertInstanceOf(SearchParameters::class, $search);
        self::assertSame('Queen', $search->artist);
        self::assertSame([3, 5], $search->tags);
        self::assertTrue($search->isFavorite);
    }

    public function testBodyArrayBecomesObjectWithoutCoercion(): void
    {
        $compiled = CompiledRequestArgument::fromParameter(
            'search',
            Parameters::parameter('search')
        );

        /** @var SearchParameters $search */
        $search = service(RequestValueCaster::class)->resolve(
            ['tags' => [3]],
            $compiled,
            coerceStrings: false
        );

        self::assertSame([3], $search->tags);
    }

    public function testQueryScalars(): void
    {
        $caster = service(RequestValueCaster::class);
        $limit = CompiledRequestArgument::fromParameter('limit', Parameters::parameter('limit'));
        $draft = CompiledRequestArgument::fromParameter('draft', Parameters::parameter('draft'));

        self::assertSame(50, $caster->resolve('50', $limit, coerceStrings: true));
        self::assertFalse($caster->resolve('false', $draft, coerceStrings: true));
    }

    public function testInvalidValuesAreBadRequests(): void
    {
        $this->expectException(InvalidRequestArgument::class);

        service(RequestValueCaster::class)->resolve(
            ['tags' => ['three']],
            CompiledRequestArgument::fromParameter('search', Parameters::parameter('search')),
            coerceStrings: true
        );
    }

    public function testInvalidScalarIsBadRequest(): void
    {
        $this->expectException(InvalidRequestArgument::class);

        service(RequestValueCaster::class)->resolve(
            'fifty',
            CompiledRequestArgument::fromParameter('limit', Parameters::parameter('limit')),
            coerceStrings: true
        );
    }
}
