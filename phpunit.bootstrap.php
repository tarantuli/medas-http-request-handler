<?php

declare(strict_types=1);

use Medas\Events\EventsPackage;
use Medas\HttpRequestHandler\HttpRequestHandlerPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        EventsPackage::instance(),
        HttpRequestHandlerPackage::instance(),
    ]);

    return $config;
});
