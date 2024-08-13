<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\AsSingleton;
use Medas\ServiceManager\{BasePackage, ServiceConfig};

class HttpRequestHandlerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfig $config): void
    {
        parent::initialize($config);

        $config->addParameterResolver(service(BodyDataResolver::class))
            ->addParameterResolver(service(QueryDataResolver::class));
    }
}
