<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional\Request;

use Medas\HttpRequestHandler\Request\BodyData;
use PHPUnit\Framework\TestCase;

class BodyDataTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $BodyData = new BodyData(['a' => 'b']);

        self::assertEquals('b', $BodyData['a']);
    }
}
