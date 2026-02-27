<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{AsSingleton, BasePackage, Interfaces\ServiceConfig};
use Medas\Files\FilesPackage;
use Medas\Json\JsonPackage;

class HttpRequestHandlerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            FilesPackage::instance(),
            JsonPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfig $config): void
    {
        parent::initialize($config);

        $config->addParameterResolver(service(DataResolvers\BodyDataResolver::class))
            ->addParameterResolver(service(DataResolvers\QueryDataResolver::class))
            ->addParameterResolver(service(DataResolvers\RequestDataObjectResolver::class));
    }
}
