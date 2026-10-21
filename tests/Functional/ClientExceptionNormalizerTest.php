<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional;

use Medas\ErrorReporting\Normalizing\ThrowableNormalizer;
use Medas\HttpRequestHandler\{Exceptions\BadRequest, ResponseDispatcher\ClientExceptionNormalizer};
use PHPUnit\Framework\TestCase;

class ClientExceptionNormalizerTest extends TestCase
{
    public function testARefusalCarriesItsMessageIdentifierAndParameters(): void
    {
        $normalized = $this->normalizer()->normalize(
            new QuantityOutOfRange(7, new \ArrayObject()),
            400
        );

        self::assertSame('quantity-out-of-range', $normalized['messageIdentifier']);
        self::assertSame(7, $normalized['messageParameters'][0]);
        self::assertIsString($normalized['messageParameters'][1]);
        self::assertArrayHasKey('message', $normalized);
        self::assertArrayHasKey('code', $normalized);
    }

    public function testAServerErrorCarriesNoMessageIdentifier(): void
    {
        $normalized = $this->normalizer()->normalize(new QuantityOutOfRange(7, null), 500);

        self::assertArrayNotHasKey('messageIdentifier', $normalized);
        self::assertArrayNotHasKey('messageParameters', $normalized);
    }

    public function testAPlainThrowableCarriesNoMessageIdentifier(): void
    {
        $normalized = $this->normalizer()->normalize(new \RuntimeException('plain'), 400);

        self::assertArrayNotHasKey('messageIdentifier', $normalized);
    }

    private function normalizer(): ClientExceptionNormalizer
    {
        return new ClientExceptionNormalizer(service(ThrowableNormalizer::class));
    }
}

class QuantityOutOfRange extends BadRequest
{
    public function pattern(): string
    {
        return 'quantity %s is out of range for %s';
    }
}
