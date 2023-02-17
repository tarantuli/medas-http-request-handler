<?php

declare(strict_types=1);

use Medas\HttpRequestHandler\HttpRequestHandlerPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        HttpRequestHandlerPackage::instance(),
    ]);

    return $config;
});
