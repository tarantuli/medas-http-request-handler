<?php

declare(strict_types=1);

namespace Medas\RequestHandler;

use Medas\ServiceManager\{AsSingleton, BasePackage};

class RequestHandlerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
