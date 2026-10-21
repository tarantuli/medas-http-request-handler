<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{AsSingleton, BasePackage, Interfaces\ServiceConfigBuilder};
use Medas\ErrorReporting\ErrorReportingPackage;
use Medas\Events\EventsPackage;
use Medas\Files\FilesPackage;
use Medas\Json\JsonPackage;
use Medas\ObjectInstantiator\ObjectInstantiatorPackage;

class HttpRequestHandlerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ErrorReportingPackage::instance(),
            FilesPackage::instance(),
            JsonPackage::instance(),
        ];
    }

    public function devDependencies(): array
    {
        return [
            EventsPackage::instance(),
            ObjectInstantiatorPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfigBuilder $config): void
    {
        parent::initialize($config);

        $config->addParameterResolver(DataResolvers\BodyDataResolver::class)
            ->addParameterResolver(DataResolvers\QueryDataResolver::class)
            ->addParameterResolver(DataResolvers\RequestDataObjectResolver::class)
            ->addParameterResolver(DataResolvers\HeaderValueResolver::class);

        $config->addManualBinding(
            ResponseDispatcher\ClientExceptionNormalizer::class,
            'isDev',
            $config->isDev
        );
    }
}
